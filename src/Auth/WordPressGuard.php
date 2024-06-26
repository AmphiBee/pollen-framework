<?php

declare(strict_types=1);

namespace Pollen\Auth;

use Illuminate\Auth\GuardHelpers;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Support\Facades\Request;
use Pollen\Models\User;
use Pollen\Support\Facades\Action;
use WP_Error;

/**
 * WordPress guard implementation for Laravel
 */
class WordPressGuard implements StatefulGuard
{
    use GuardHelpers;

    /**
     * Get the last user we attempted to login as.
     *
     * @var User
     */
    private $lastAttempted = null;

    /**
     * Determine if the current user is authenticated.
     *
     * @return bool
     */
    public function check()
    {
        return is_user_logged_in();
    }

    /**
     * Get the currently authenticated user.
     *
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function user()
    {
        return $this->user ??= $this->check() ? User::find(get_current_user_id()) : null;
    }

    /**
     * Validate a user's credentials.
     *
     *
     * @return bool
     */
    public function validate(array $credentials = [])
    {
        $user = wp_authenticate($credentials['username'], $credentials['password']);
        $this->lastAttempted = User::find($user->ID);

        return ! ($user instanceof WP_Error);
    }

    /**
     * Attempt to authenticate a user using the given credentials.
     *
     * @param  bool  $remember
     * @param  bool  $login
     * @return bool
     */
    public function attempt(array $credentials = [], $remember = false, $login = true)
    {
        $validate = $this->validate($credentials);

        if ($validate && $login) {
            $user = $this->lastAttempted;
            wp_set_auth_cookie($user->ID, $credentials['remember'], Request::secure());
            Action::run('wp_login', $user->user_login, $user);
            $this->setUser($user);
        }

        return $validate;
    }

    /**
     * Log a user into the application without sessions or cookies.
     *
     *
     * @return bool
     */
    public function once(array $credentials = [])
    {
        if ($this->validate($credentials)) {
            $this->setUser($this->lastAttempted);

            return true;
        }

        return false;
    }

    /**
     * Log a user into the application.
     *
     * @param  bool  $remember
     * @return void
     */
    public function login(Authenticatable $user, $remember = false)
    {
        wp_set_auth_cookie($user->ID, $remember);
        Action::run('wp_login', $user->user_login, get_userdata($user->ID));
        $this->setUser($user);
        wp_set_current_user($user->ID);
    }

    /**
     * Log the given user ID into the application.
     *
     * @param  mixed  $id
     * @param  bool  $remember
     * @return \Illuminate\Contracts\Auth\Authenticatable|bool
     */
    public function loginUsingId($id, $remember = false)
    {
        if ($user = User::find($id)) {
            wp_set_auth_cookie($user->ID, $remember);
            Action::run('wp_login', $user->user_login, get_userdata($user->ID));
            $this->setUser($user);
            wp_set_current_user($user->ID);
        }

        return $this->user ?? null;
    }

    /**
     * Log the given user ID into the application without sessions or cookies.
     *
     * @param  mixed  $id
     * @return bool
     */
    public function onceUsingId($id)
    {
        if ($user = User::find($id)) {
            wp_set_current_user($id);
            $this->setUser($user);

            return true;
        }

        return false;
    }

    /**
     * Determine if the user was authenticated via "remember me" cookie.
     *
     * @return bool
     */
    public function viaRemember()
    {
        return Request::hasCookie(Request::secure() ? SECURE_AUTH_COOKIE : AUTH_COOKIE);
    }

    /**
     * Log the user out of the application.
     *
     * @return void
     */
    public function logout()
    {
        wp_logout();
        $this->user = null;
    }

    /**
     * Set the current user.
     *
     *
     * @return $this
     */
    public function setUser(Authenticatable $user)
    {
        wp_set_current_user($user->ID);
        $this->user = $user;

        return $this;
    }
}
