<?php
/**
 * An helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace Friluft{
/**
 * Friluft\Field
 *
 * @property integer $id 
 * @property string $name 
 * @property string $type 
 * @property string $options 
 * @property integer $attribute_id 
 * @property-read \Friluft\Attribute $attribute 
 * @method static \Illuminate\Database\Query\Builder|\Friluft\Field whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\Friluft\Field whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\Friluft\Field whereType($value)
 * @method static \Illuminate\Database\Query\Builder|\Friluft\Field whereOptions($value)
 * @method static \Illuminate\Database\Query\Builder|\Friluft\Field whereAttributeId($value)
 */
	class Field {}
}

namespace Friluft{
/**
 * Friluft\Attribute
 *
 * @property integer $id 
 * @property string $name 
 * @property string $machine 
 * @property-read \Illuminate\Database\Eloquent\Collection|\Friluft\Field[] $fields 
 * @property-read \Illuminate\Database\Eloquent\Collection|\Friluft\Product[] $products 
 * @method static \Illuminate\Database\Query\Builder|\Friluft\Attribute whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\Friluft\Attribute whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\Friluft\Attribute whereMachine($value)
 */
	class Attribute {}
}

namespace Friluft{
/**
 * Friluft\User
 *
 * @property integer $id 
 * @property string $remember_token 
 * @property \Carbon\Carbon $created_at 
 * @property \Carbon\Carbon $updated_at 
 * @property string $deleted_at 
 * @property string $firstname 
 * @property string $lastname 
 * @property string $email 
 * @property string $password 
 * @property integer $role_id 
 * @property-read \Friluft\Role $role 
 * @property-read \Illuminate\Database\Eloquent\Collection|\Friluft\Order[] $orders 
 * @property-read \Illuminate\Database\Eloquent\Collection|\Friluft\Address[] $addresses 
 * @method static \Illuminate\Database\Query\Builder|\Friluft\User whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\Friluft\User whereRememberToken($value)
 * @method static \Illuminate\Database\Query\Builder|\Friluft\User whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\Friluft\User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\Friluft\User whereDeletedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\Friluft\User whereFirstname($value)
 * @method static \Illuminate\Database\Query\Builder|\Friluft\User whereLastname($value)
 * @method static \Illuminate\Database\Query\Builder|\Friluft\User whereEmail($value)
 * @method static \Illuminate\Database\Query\Builder|\Friluft\User wherePassword($value)
 * @method static \Illuminate\Database\Query\Builder|\Friluft\User whereRoleId($value)
 */
	class User {}
}

namespace Friluft{
/**
 * Friluft\Role
 *
 * @property integer $id 
 * @property string $name 
 * @method static \Illuminate\Database\Query\Builder|\Friluft\Role whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\Friluft\Role whereName($value)
 */
	class Role {}
}

namespace Friluft{
/**
 * Friluft\Category
 *
 * @property integer $id 
 * @property \Carbon\Carbon $created_at 
 * @property \Carbon\Carbon $updated_at 
 * @property string $name 
 * @property string $slug 
 * @property integer $parent_id 
 * @property integer $order 
 * @property-read \Illuminate\Database\Eloquent\Collection|\Friluft\Product[] $products 
 * @property-read \Illuminate\Database\Eloquent\Collection|\Friluft\Category[] $children 
 * @property-read \Friluft\Category $parent 
 * @method static \Illuminate\Database\Query\Builder|\Friluft\Category whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\Friluft\Category whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\Friluft\Category whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\Friluft\Category whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\Friluft\Category whereSlug($value)
 * @method static \Illuminate\Database\Query\Builder|\Friluft\Category whereParentId($value)
 * @method static \Illuminate\Database\Query\Builder|\Friluft\Category whereOrder($value)
 * @method static \Friluft\Category root()
 */
	class Category {}
}

namespace Friluft{
/**
 * Friluft\Store
 *
 * @property integer $id 
 * @property string $machine 
 * @property string $name 
 * @property string $url 
 * @method static \Illuminate\Database\Query\Builder|\Friluft\Store whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\Friluft\Store whereMachine($value)
 * @method static \Illuminate\Database\Query\Builder|\Friluft\Store whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\Friluft\Store whereUrl($value)
 */
	class Store {}
}

namespace Friluft{
/**
 * Friluft\Order
 *
 * @property integer $id 
 * @property \Carbon\Carbon $created_at 
 * @property \Carbon\Carbon $updated_at 
 * @property string $data 
 * @property integer $user_id 
 * @property-read \Friluft\User $user 
 * @method static \Illuminate\Database\Query\Builder|\Friluft\Order whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\Friluft\Order whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\Friluft\Order whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\Friluft\Order whereData($value)
 * @method static \Illuminate\Database\Query\Builder|\Friluft\Order whereUserId($value)
 */
	class Order {}
}

namespace Friluft{
/**
 * Friluft\Address
 *
 * @property integer $id 
 * @property integer $user_id 
 * @property-read \Friluft\User $user 
 * @method static \Illuminate\Database\Query\Builder|\Friluft\Address whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\Friluft\Address whereUserId($value)
 */
	class Address {}
}

namespace Friluft{
/**
 * Friluft\Product
 *
 * @property integer $id 
 * @property \Carbon\Carbon $created_at 
 * @property \Carbon\Carbon $updated_at 
 * @property string $name 
 * @property string $brand 
 * @property string $model 
 * @property float $weight 
 * @property string $description 
 * @property float $price 
 * @property float $in_price 
 * @property float $tax_percentage 
 * @property integer $stock 
 * @property string $slug 
 * @property string $images 
 * @property boolean $enabled 
 * @property string $summary 
 * @property-read \Illuminate\Database\Eloquent\Collection|\Friluft\Category[] $categories 
 * @method static \Illuminate\Database\Query\Builder|\Friluft\Product whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\Friluft\Product whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\Friluft\Product whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\Friluft\Product whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\Friluft\Product whereBrand($value)
 * @method static \Illuminate\Database\Query\Builder|\Friluft\Product whereModel($value)
 * @method static \Illuminate\Database\Query\Builder|\Friluft\Product whereWeight($value)
 * @method static \Illuminate\Database\Query\Builder|\Friluft\Product whereDescription($value)
 * @method static \Illuminate\Database\Query\Builder|\Friluft\Product wherePrice($value)
 * @method static \Illuminate\Database\Query\Builder|\Friluft\Product whereInPrice($value)
 * @method static \Illuminate\Database\Query\Builder|\Friluft\Product whereTaxPercentage($value)
 * @method static \Illuminate\Database\Query\Builder|\Friluft\Product whereStock($value)
 * @method static \Illuminate\Database\Query\Builder|\Friluft\Product whereSlug($value)
 * @method static \Illuminate\Database\Query\Builder|\Friluft\Product whereImages($value)
 * @method static \Illuminate\Database\Query\Builder|\Friluft\Product whereEnabled($value)
 * @method static \Illuminate\Database\Query\Builder|\Friluft\Product whereSummary($value)
 * @method static \Friluft\Product enabled()
 * @method static \Friluft\Product search($search, $threshold = null)
 */
	class Product {}
}

