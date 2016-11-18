<?php

namespace Silber\Bouncer\Database;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Silber\Bouncer\Database\Constraints\Abilities as AbilitiesConstraint;

class Role extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

	use SoftDeletes;
	 
    protected $fillable = ['name'];
	
	protected $appends = [
		'abilities'
    ];

    protected $unassignedDate = ['deleted_at'];


    /**
     * {@inheritDoc}
     */
    public function __construct(array $attributes = [])
    {
        $this->table = Models::table('roles');

        parent::__construct($attributes);
    }

    /**
     * The abilities relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function abilities()
    {
        return $this->belongsToMany(
            Models::classname(Ability::class),
            Models::table('role_abilities')
        );
    }

    /**
     * The users relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany(
            Models::classname(User::class),
            Models::table('user_roles')
        );
    }

    /**
     * Constrain the given query by the provided ability.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $ability
     * @param  \Illuminate\Database\Eloquent\Model|string|null  $model
     * @return void
     */
    public function scopeWhereCan($query, $ability, $model = null)
    {
        (new AbilitiesConstraint)->constrainRoles($query, $ability, $model);
    }
	public function getAbilitiesAttribute() {
		$role = $this->find($this->id);
		return $role->abilities()->pluck('name');
	}
}
