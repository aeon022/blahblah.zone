<?php

namespace Modules\User\Models\User\Attribute;

use Carbon\Carbon;
/**
 * Trait UserAttribute
 *
 * The Attribute is Defined For User.
 *
 * PHP version 7.1.3
 *
 * @category  Administration
 * @package   Modules\User
 * @author    Vipul Patel <vipul@chetsapp.com>
 * @copyright 2019 Chetsapp Group
 * @license   Chetsapp Private Limited
 * @version   Release: @1.0@
 * @link      http://chetsapp.com
 * @since     Class available since Release 1.0
 */
trait UserAttribute
{
    /**
     * This mutator automatically return boolean.
     *
     * @return Boolean
     */
    public function isActive()
    {
        return $this->activated == 1;
    }

    /**
     * This mutator automatically return boolean.
     *
     * @return Boolean
     */
    public function isEmailVerified()
    {
        return $this->email_verified == 1;
    }

    /**
     * This mutator automatically hashes the password.
     *
     * @param String $value [Password]
     *
     * @return String
     */
    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = \Hash::make($value);
    }

    /**
     * This mutator automatically remove space from email.
     *
     * @param String $value [Email]
     *
     * @return String
     */
    public function setEmailAttribute($value)
    {
        $this->attributes['email'] = trim(strtolower($value));
    }

    /**
     * This mutator automatically remove space from User name.
     *
     * @param String $value [Username]
     *
     * @return String
     */
    public function setUsernameAttribute($value)
    {
        $this->attributes['username'] = trim($value);
    }

    /**
     * This mutator automatically set date format for date of birth.
     *
     * @param String $value [Date of birth]
     *
     * @return Date.
     */
    public function setDobAttribute($value)
    {
        $this->attributes['dob'] = (new Carbon($value))->format('Y-m-d');
    }

    /**
     * This mutator automatically set date format for joining date.
     *
     * @param String $value [Joining date]
     *
     * @return Date.
     */
    public function setJoiningDateAttribute($value)
    {
        $this->attributes['joining_date'] = (new Carbon($value))->format('Y-m-d');
    }

    /**
     * This mutator automatically set convert country to number.
     *
     * @param String $value [Joining date]
     *
     * @return Date.
     */
    public function getCountryAttribute($value)
    {
        return intval($value);
    }
}
