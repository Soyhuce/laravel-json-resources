<?php declare(strict_types=1);

namespace Soyhuce\JsonResources\Tests\Fixtures;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;

/**
 * @method \Soyhuce\JsonResources\Tests\Fixtures\User createOne($attributes = [])
 * @method \Illuminate\Database\Eloquent\Collection|\Soyhuce\JsonResources\Tests\Fixtures\User create($attributes = [], ?Model $parent = null)
 */
class UserFactory extends Factory
{
    protected $model = User::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return ['email' => $this->faker->safeEmail()];
    }
}
