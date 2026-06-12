<?php
require_once __DIR__ . '/BaseModel.php';

class Shipping extends BaseModel {
    protected string $table = 'shipping_details';

    public function __construct() {
        parent::__construct();
    }

    public function saveDetails(int $userId, array $data): int {
        $existing = $this->findBy('user_id', $userId);
        if ($existing) {
            $this->update($existing['id'], [
                'full_name'       => $data['full_name'],
                'email'           => $data['email'],
                'phone'           => $data['phone'],
                'county'          => $data['county'],
                'city'            => $data['city'],
                'address'         => $data['address'],
                'apartment'       => $data['apartment'] ?? null,
                'postal_code'     => $data['postal_code'] ?? null,
                'delivery_option' => $data['delivery_option'] ?? 'standard',
                'order_notes'     => $data['order_notes'] ?? null,
            ]);
            return $existing['id'];
        }
        return $this->create([
            'user_id'         => $userId,
            'full_name'       => $data['full_name'],
            'email'           => $data['email'],
            'phone'           => $data['phone'],
            'county'          => $data['county'],
            'city'            => $data['city'],
            'address'         => $data['address'],
            'apartment'       => $data['apartment'] ?? null,
            'postal_code'     => $data['postal_code'] ?? null,
            'delivery_option' => $data['delivery_option'] ?? 'standard',
            'order_notes'     => $data['order_notes'] ?? null,
        ]);
    }

    public static function calculateShipping(float $subtotal): array {
        $threshold = SHIPPING_THRESHOLD;
        $flatRate  = SHIPPING_FLAT_RATE;

        if ($subtotal >= $threshold) {
            return [
                'cost'        => 0.00,
                'is_free'     => true,
                'label'       => 'FREE',
                'remaining'   => 0.00,
                'threshold'   => $threshold,
            ];
        }

        return [
            'cost'        => $flatRate,
            'is_free'     => false,
            'label'       => CURRENCY_SYMBOL . number_format($flatRate, 0),
            'remaining'   => $threshold - $subtotal,
            'threshold'   => $threshold,
        ];
    }

    public static function getDeliveryOptions(): array {
        return [
            'standard' => [
                'label' => 'Standard Delivery',
                'days'  => '5-8 business days',
                'icon'  => 'bi-truck',
            ],
            'express' => [
                'label' => 'Express Delivery',
                'days'  => '1-3 business days',
                'icon'  => 'bi-lightning',
            ],
        ];
    }

    public static function getKenyanCounties(): array {
        return [
            'Baringo', 'Bomet', 'Bungoma', 'Busia', 'Elgeyo-Marakwet',
            'Embu', 'Garissa', 'Homa Bay', 'Isiolo', 'Kajiado',
            'Kakamega', 'Kericho', 'Kiambu', 'Kilifi', 'Kirinyaga',
            'Kisii', 'Kisumu', 'Kitui', 'Kwale', 'Laikipia',
            'Lamu', 'Machakos', 'Makueni', 'Mandera', 'Marsabit',
            'Meru', 'Migori', 'Mombasa', "Murang'a", 'Nairobi City',
            'Nakuru', 'Nandi', 'Narok', 'Nyamira', 'Nyandarua',
            'Nyeri', 'Samburu', 'Siaya', 'Taita-Taveta', 'Tana River',
            'Tharaka-Nithi', 'Trans-Nzoia', 'Turkana', 'Uasin Gishu',
            'Vihiga', 'Wajir', 'West Pokot',
        ];
    }

    public function getByUser(int $userId): ?array {
        return $this->findBy('user_id', $userId);
    }

    public function prepareForAI(array $shipping): array {
        return [
            'estimated_delivery'     => '-- AI will calculate based on county and delivery option --',
            'smart_address_suggestions' => '-- AI-powered address autocomplete placeholder --',
            'delivery_recommendation' => '-- AI will recommend optimal delivery option --',
            'delivery_date_range'    => [
                'min' => '-- AI computed --',
                'max' => '-- AI computed --',
            ],
            'nearest_drop_center'    => '-- AI-powered nearest pickup point --',
        ];
    }
}
