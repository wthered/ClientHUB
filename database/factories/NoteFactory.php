<?php

    namespace Database\Factories;

    use App\Models\Account;
    use App\Models\Note;
    use App\Models\Opportunities\Opportunity;
    use App\Models\Users\User;
    use Illuminate\Database\Eloquent\Factories\Factory;

    /**
     * @extends Factory<Note>
     */
    class NoteFactory extends Factory {
	    protected $model = Note::class;

	    public function definition(): array {
		    return [
			    'user_id'      => User::inRandomOrder()->first()?->id ?? User::factory(),
			    'content'      => $this->faker->paragraph(),
			    'notable_id'   => null, // Θα ορίζεται από τα states
			    'notable_type' => null,
		    ];
	    }

	    /**
	     * State για Note που ανήκει σε Account
	     */
	    public function forAccount(Account $account = null): static {
		    return $this->state(fn (array $attributes) => [
			    'notable_id'   => $account?->id ?? Account::factory(),
			    'notable_type' => Account::class,
			    'content'      => "Σημείωση για την εταιρεία: " . ($account?->name ?? 'Πελάτης') . ". " . $this->faker->sentence(),
		    ]);
	    }

	    /**
	     * State για Note που ανήκει σε Opportunity
	     */
	    public function forOpportunity(Opportunity $opportunity = null): static {
		    return $this->state(fn (array $attributes) => [
			    'notable_id'   => $opportunity?->id ?? Opportunity::factory(),
			    'notable_type' => Opportunity::class,
			    'content'      => "Deal Update [{$opportunity?->name}]: " . $this->faker->sentence(),
		    ]);
	    }
    }
