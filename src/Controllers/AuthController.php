<?php

namespace Controllers;

use Enums\Errors;
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
        $resp = ['errors' => [Errors::IncompleteData->value]];
        $user = new User();
        $data = $this->request->getInputHandler()->getOriginalPost();
        if (isset($data['email']) && isset($data['password'])) {
            $auth = $user->authUser($data['email'], $data['password']);
            if ($auth) {
                $token = $user->getToken($data['email']);
                $resp = ['token' => $token];
            } else {
                $resp = ['errors' => [Errors::Credentials->value]];
            }
        }
        return $this->response->json($resp);
    }

    /**
     * Requests data checks from the request, tries to create a user, returns json in the response.
     * @return Pecee\Http\Response
     */
    public function registration(): Response
    {
        $resp = ['errors' => [Errors::IncompleteData->value]];
        $data = $this->request->getInputHandler()->getOriginalPost();
        if (isset($data['email']) && isset($data['password']) && isset($data['password_confirm'])) {
            if ($data['password'] == $data['password_confirm']) {
                $check = new RequestDataCheck();
                if ($check->checkEmailUniqueness($data['email'])) {
                    if ($check->checkingPassword($data['password'])) {
                        $user = new User();
                        $result = $user->save($data['email'], $data['password']);
                        if ($result) {
                            $tokenSetter = new TokenSetter();
                            $tokenSetter->setToken($data['email']);
                        }
                        $resp = $result ? ['response' => 'User created.'] : $resp;
                    } else {
                        $resp = ['errors' => ['Email ' . Errors::BadPassword->value]];
                    }
                } else {
                    $resp = ['errors' => ['Email ' . Errors::Unique->value]];
                }
            } else {
                $resp = ['errors' => [Errors::ConfirmPassword->value]];;
            }
        }
        return $this->response->json($resp);
    }
}
