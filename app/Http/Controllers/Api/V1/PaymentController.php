<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProcessPaymentRequest;
use App\Http\Resources\PaymentResource;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    /**
     * Display payments for an order.
     */
    public function index(Order $order): JsonResponse
    {
        $this->authorize('view', $order);

        $payments = $order->payments()
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => PaymentResource::collection($payments),
            'meta' => [
                'order_id' => $order->id,
                'order_total' => $order->total_amount,
                'paid_amount' => $payments->where('status', 'completed')->sum('amount'),
                'remaining_amount' => max(0, $order->total_amount - $payments->where('status', 'completed')->sum('amount')),
                'timestamp' => now()->toISOString(),
                'version' => 'v1'
            ]
        ]);
    }

    /**
     * Process a payment for an order.
     */
    public function store(ProcessPaymentRequest $request, Order $order): JsonResponse
    {
        $this->authorize('update', $order);

        // Check if order can accept payments
        if ($order->status === 'draft') {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'ORDER_NOT_READY_FOR_PAYMENT',
                    'message' => 'Cannot process payment for draft orders. Please set order to open status first.',
                ],
                'meta' => [
                    'timestamp' => now()->toISOString(),
                    'version' => 'v1'
                ]
            ], 422);
        }

        // Check if order is already fully paid
        $paidAmount = $order->payments()->completed()->sum('amount');
        $remainingAmount = $order->total_amount - $paidAmount;

        if ($remainingAmount <= 0) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'ORDER_ALREADY_PAID',
                    'message' => 'This order is already fully paid.',
                ],
                'meta' => [
                    'timestamp' => now()->toISOString(),
                    'version' => 'v1'
                ]
            ], 422);
        }

        // Validate payment amount doesn't exceed remaining amount
        if ($request->input('amount') > $remainingAmount) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'PAYMENT_AMOUNT_EXCEEDS_REMAINING',
                    'message' => 'Payment amount exceeds the remaining order balance.',
                    'details' => [
                        'remaining_amount' => $remainingAmount,
                        'requested_amount' => $request->input('amount'),
                    ]
                ],
                'meta' => [
                    'timestamp' => now()->toISOString(),
                    'version' => 'v1'
                ]
            ], 422);
        }

        try {
            DB::beginTransaction();

            $payment = $order->payments()->create([
                'store_id' => $order->store_id,
                'payment_method' => $request->input('payment_method'),
                'amount' => $request->input('amount'),
                'reference_number' => $request->input('reference_number'),
                'status' => 'pending',
                'notes' => $request->input('notes'),
            ]);

            // Process payment based on method
            $this->processPaymentByMethod($payment, $request->validated());

            // Check if order is now fully paid and complete it
            $totalPaid = $order->payments()->completed()->sum('amount');
            if ($totalPaid >= $order->total_amount && $order->status !== 'completed') {
                $order->complete();
            }

            DB::commit();

            $payment->load('order');

            return response()->json([
                'success' => true,
                'data' => new PaymentResource($payment),
                'message' => 'Payment processed successfully',
                'meta' => [
                    'order_total' => $order->total_amount,
                    'total_paid' => $order->payments()->completed()->sum('amount'),
                    'remaining_amount' => max(0, $order->total_amount - $order->payments()->completed()->sum('amount')),
                    'timestamp' => now()->toISOString(),
                    'version' => 'v1'
                ]
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Payment processing failed', [
                'order_id' => $order->id,
                'payment_method' => $request->input('payment_method'),
                'amount' => $request->input('amount'),
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'PAYMENT_PROCESSING_FAILED',
                    'message' => 'Failed to process payment. Please try again.',
                    'details' => config('app.debug') ? $e->getMessage() : null
                ],
                'meta' => [
                    'timestamp' => now()->toISOString(),
                    'version' => 'v1'
                ]
            ], 500);
        }
    }

    /**
     * Display the specified payment.
     */
    public function show(Order $order, Payment $payment): JsonResponse
    {
        $this->authorize('view', $order);

        if ($payment->order_id !== $order->id) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'PAYMENT_NOT_FOUND',
                    'message' => 'Payment not found for this order.',
                ],
                'meta' => [
                    'timestamp' => now()->toISOString(),
                    'version' => 'v1'
                ]
            ], 404);
        }

        $payment->load(['order', 'refunds']);

        return response()->json([
            'success' => true,
            'data' => new PaymentResource($payment),
            'meta' => [
                'timestamp' => now()->toISOString(),
                'version' => 'v1'
            ]
        ]);
    }

    /**
     * Get payment methods configuration.
     */
    public function methods(): JsonResponse
    {
        $this->authorize('viewAny', Payment::class);

        $methods = [
            [
                'id' => 'cash',
                'name' => 'Cash',
                'description' => 'Cash payment',
                'requires_reference' => false,
                'is_active' => true,
            ],
            [
                'id' => 'card',
                'name' => 'Credit/Debit Card',
                'description' => 'Card payment via EDC',
                'requires_reference' => true,
                'is_active' => true,
            ],
            [
                'id' => 'qris',
                'name' => 'QRIS',
                'description' => 'QR Code payment',
                'requires_reference' => true,
                'is_active' => true,
            ],
            [
                'id' => 'bank_transfer',
                'name' => 'Bank Transfer',
                'description' => 'Direct bank transfer',
                'requires_reference' => true,
                'is_active' => true,
            ],
            [
                'id' => 'e_wallet',
                'name' => 'E-Wallet',
                'description' => 'Digital wallet payment',
                'requires_reference' => true,
                'is_active' => true,
            ],
        ];

        return response()->json([
            'success' => true,
            'data' => $methods,
            'message' => 'Payment methods retrieved successfully',
            'meta' => [
                'timestamp' => now()->toISOString(),
                'version' => 'v1'
            ]
        ]);
    }

    /**
     * Generate receipt for an order.
     */
    public function receipt(Order $order): JsonResponse
    {
        $this->authorize('view', $order);

        if ($order->status !== 'completed') {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'ORDER_NOT_COMPLETED',
                    'message' => 'Receipt can only be generated for completed orders.',
                ],
                'meta' => [
                    'timestamp' => now()->toISOString(),
                    'version' => 'v1'
                ]
            ], 422);
        }

        $order->load(['items.product', 'payments', 'member', 'table', 'user:id,name']);

        $receipt = [
            'order' => [
                'id' => $order->id,
                'order_number' => $order->order_number,
                'date' => $order->created_at->format('Y-m-d H:i:s'),
                'cashier' => $order->user->name,
                'table' => $order->table ? $order->table->table_number : null,
                'member' => $order->member ? [
                    'name' => $order->member->name,
                    'member_number' => $order->member->member_number,
                ] : null,
            ],
            'items' => $order->items->map(function ($item) {
                return [
                    'name' => $item->product_name,
                    'quantity' => $item->quantity,
                    'unit_price' => $item->unit_price,
                    'total_price' => $item->total_price,
                    'options' => $item->product_options,
                    'notes' => $item->notes,
                ];
            }),
            'totals' => [
                'subtotal' => $order->subtotal,
                'tax_amount' => $order->tax_amount,
                'service_charge' => $order->service_charge,
                'discount_amount' => $order->discount_amount,
                'total_amount' => $order->total_amount,
            ],
            'payments' => $order->payments->map(function ($payment) {
                return [
                    'method' => $payment->payment_method,
                    'amount' => $payment->amount,
                    'reference' => $payment->reference_number,
                    'processed_at' => $payment->processed_at?->format('Y-m-d H:i:s'),
                ];
            }),
            'store' => [
                'name' => $order->store->name ?? 'POS Store',
                'address' => $order->store->address ?? '',
                'phone' => $order->store->phone ?? '',
            ],
        ];

        return response()->json([
            'success' => true,
            'data' => $receipt,
            'message' => 'Receipt generated successfully',
            'meta' => [
                'timestamp' => now()->toISOString(),
                'version' => 'v1'
            ]
        ]);
    }

    /**
     * Process payment based on method.
     */
    private function processPaymentByMethod(Payment $payment, array $data): void
    {
        switch ($payment->payment_method) {
            case 'cash':
                $this->processCashPayment($payment, $data);
                break;
            case 'card':
                $this->processCardPayment($payment, $data);
                break;
            case 'qris':
                $this->processQrisPayment($payment, $data);
                break;
            case 'bank_transfer':
                $this->processBankTransferPayment($payment, $data);
                break;
            case 'e_wallet':
                $this->processEWalletPayment($payment, $data);
                break;
            default:
                throw new \InvalidArgumentException('Unsupported payment method: ' . $payment->payment_method);
        }
    }

    /**
     * Process cash payment.
     */
    private function processCashPayment(Payment $payment, array $data): void
    {
        // Cash payments are immediately processed
        $payment->markAsProcessed();
    }

    /**
     * Process card payment.
     */
    private function processCardPayment(Payment $payment, array $data): void
    {
        // In a real implementation, this would integrate with a payment gateway
        // For now, we'll simulate successful processing
        $payment->markAsProcessed();
    }

    /**
     * Process QRIS payment.
     */
    private function processQrisPayment(Payment $payment, array $data): void
    {
        // In a real implementation, this would integrate with QRIS providers
        // For now, we'll simulate successful processing
        $payment->markAsProcessed();
    }

    /**
     * Process bank transfer payment.
     */
    private function processBankTransferPayment(Payment $payment, array $data): void
    {
        // Bank transfers might need manual verification
        // For now, we'll mark as processed
        $payment->markAsProcessed();
    }

    /**
     * Process e-wallet payment.
     */
    private function processEWalletPayment(Payment $payment, array $data): void
    {
        // In a real implementation, this would integrate with e-wallet providers
        // For now, we'll simulate successful processing
        $payment->markAsProcessed();
    }
}