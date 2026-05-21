<?php

    namespace Database\Factories;

    use App\Models\Client;
    use Illuminate\Database\Eloquent\Factories\Factory;

    /**
     * @extends Factory<Client>
     */
    class ClientFactory extends Factory {
        /**
         * Define the model's default state.
         *
         * @return array<string, mixed>
         */
        public function definition(): array {
            return [
                'name'      => $this->faker->company,
                'email'     => $this->faker->companyEmail,
                'phone'     => $this->faker->phoneNumber,
                'website'   => $this->faker->url,
                'address'   => $this->faker->address,
                'notes'     => $this->faker->sentence,
                'is_active' => $this->faker->boolean(95),
            ];
        }
    }
