<?php

namespace Controllers;

use Models\User;
use Pecee\Http\Response;
use Service\RequestDataCheck;
use Service\TokenSetter;

class AuthController extends AbstractController
{
    /**
     * Checks the email and password from the request. If successful, returns the user token.
     * @return Pecee\Http\Response
     */
    public function login(): Response
    {
        $user = new User();
        $data = $this->request->getInputHandler()->getOriginalPost();
        $auth = $user->authUser($data['email'], $data['password']);
        if($auth) {
            $token = $user->getToken($data['email']);
        }
        return $this->response->json(['token' => $token]);
    }

    /**
     * Requests data checks from the request, tries to create a user, returns json in the response.
     * @return Pecee\Http\Response
     */
    public function registration(): Response
    {
        $resp = 'error';
        $data = $this->request->getInputHandler()->getOriginalPost();
        if ($data['password'] == $data['password_confirm']) {
            $check = new RequestDataCheck();
            if ($check->checkEmailUniqueness($data['email'])) {
                if ($check->checkingPassword($data['password'])) {
                    $user = new User();
                    $result = $user->save($data['email'], $data['password']);
                    if($result) {
                        $tokenSetter = new TokenSetter();
                        $tokenSetter->setToken($data['email']);
                    }
                    $resp = $result ? 'User created.' : $resp;
                } else {
                    $resp = 'Invalid characters in the password or shorter than 8 characters.';
                }
            } else {
                $resp = 'Email is not unique!';
            }
        } else {
            $resp = 'Password mismatch.';
        }
        return $this->response->json(['response' => $resp]);
    }
}
