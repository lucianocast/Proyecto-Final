{{-- Ejemplo de integración de Google Pay en el checkout --}}
<div x-data="googlePayHandler()" x-init="init()">
    {{-- Botón de Google Pay --}}
    <div id="google-pay-button-container" class="mt-4"></div>

    @push('scripts')
    <script src="https://pay.google.com/gp/p/js/pay.js"></script>
    <script>
        function googlePayHandler() {
            return {
                paymentsClient: null,
                
                init() {
                    // Configuración base de Google Pay
                    const baseRequest = {
                        apiVersion: 2,
                        apiVersionMinor: 0
                    };

                    // Inicializar cliente de pagos
                    this.paymentsClient = new google.payments.api.PaymentsClient({
                        environment: '{{ config("services.google_pay.sandbox") ? "TEST" : "PRODUCTION" }}'
                    });

                    // Mostrar botón si es compatible
                    this.paymentsClient.isReadyToPay({
                        ...baseRequest,
                        allowedPaymentMethods: this.getAllowedPaymentMethods()
                    })
                    .then(response => {
                        if (response.result) {
                            this.addGooglePayButton();
                        }
                    })
                    .catch(err => {
                        console.error('Error checking Google Pay availability:', err);
                    });
                },

                getAllowedPaymentMethods() {
                    return [{
                        type: 'CARD',
                        parameters: {
                            allowedAuthMethods: ['PAN_ONLY', 'CRYPTOGRAM_3DS'],
                            allowedCardNetworks: ['MASTERCARD', 'VISA']
                        },
                        tokenizationSpecification: {
                            type: 'PAYMENT_GATEWAY',
                            parameters: {
                                gateway: 'example', // Reemplazar con tu gateway
                                gatewayMerchantId: '{{ config("services.google_pay.merchant_id") }}'
                            }
                        }
                    }];
                },

                addGooglePayButton() {
                    const button = this.paymentsClient.createButton({
                        onClick: () => this.onGooglePaymentButtonClicked(),
                        buttonColor: 'default',
                        buttonType: 'pay'
                    });
                    
                    document.getElementById('google-pay-button-container').appendChild(button);
                },

                onGooglePaymentButtonClicked() {
                    const paymentDataRequest = {
                        apiVersion: 2,
                        apiVersionMinor: 0,
                        allowedPaymentMethods: this.getAllowedPaymentMethods(),
                        transactionInfo: {
                            totalPriceStatus: 'FINAL',
                            totalPrice: '{{ $total }}',
                            currencyCode: 'ARS',
                            countryCode: 'AR'
                        },
                        merchantInfo: {
                            merchantName: 'Pastelería',
                            merchantId: '{{ config("services.google_pay.merchant_id") }}'
                        }
                    };

                    this.paymentsClient.loadPaymentData(paymentDataRequest)
                        .then(paymentData => {
                            this.processPayment(paymentData);
                        })
                        .catch(err => {
                            console.error('Payment failed:', err);
                            @this.dispatch('paymentError', { message: 'Error al procesar el pago' });
                        });
                },

                processPayment(paymentData) {
                    // Extraer token de pago
                    const paymentToken = paymentData.paymentMethodData.tokenizationData.token;
                    
                    // Datos adicionales
                    const additionalData = {
                        email: paymentData.email || '',
                        billing_address: paymentData.paymentMethodData.info?.billingAddress || {}
                    };

                    // Llamar al método Livewire
                    @this.handlePaymentToken(paymentToken, additionalData);
                }
            }
        }

        // Escuchar eventos de Livewire
        document.addEventListener('livewire:init', () => {
            Livewire.on('paymentSuccess', (event) => {
                alert(event.message);
                // Opcional: redirigir o mostrar mensaje de éxito
            });

            Livewire.on('paymentError', (event) => {
                alert('Error: ' + event.message);
            });
        });
    </script>
    @endpush
</div>
