<?php declare(strict_types=1);

namespace Soyhuce\JsonResources\Tests\Fixtures;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $email
 * @method static \Soyhuce\JsonResources\Tests\Fixtures\UserFactory factory($count = 1, $state = [])
 */
class User extends Model
{
    use HasFactory;

    /** @var bool */
    protected static $unguarded = true;
}
