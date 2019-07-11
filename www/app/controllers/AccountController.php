<?php

namespace Shoutzor\Controller;

class AccountController extends ControllerBase
{
    public function initialize()
    {
      $this->view->setTemplateAfter('common');
      $this->addBaseAssets();
    }

    /**
     * Account settings (when logged in)
     */
    public function indexAction()
    {
    }

    /**
     * Register a new user
     */
    public function registerAction()
    {
      $form = new RegisterForm;

      if ($this->request->isPost())
      {
        $name           = $this->request->getPost('name', ['string', 'striptags']);
        $username       = $this->request->getPost('username', 'alphanum');
        $email          = $this->request->getPost('email', 'email');
        $password       = $this->request->getPost('password');
        $repeatPassword = $this->request->getPost('repeatPassword');

        if(strlen($username) > 25)
        {
          $this->flash->error('The username cannot be longer then 25 characters');
          return false;
        }

        if ($password != $repeatPassword)
        {
          $this->flash->error('The provided passwords don\'t match');
          return false;
        }1

        $user = new Users();
        $user->username   = $username;
        $user->password   = password_hash($password, PASSWORD_ARGON2I);
        $user->name       = $name;
        $user->email      = $email;
        $user->created_at = new Phalcon\Db\RawValue('now()');
        $user->banned     = 0;
        $user->verified   = 1; //TODO email-verification

        if ($user->save() == false) {
          foreach ($user->getMessages() as $message) {
            $this->flash->error((string) $message);
          }
        } else {
          $this->tag->setDefault('email', '');
          $this->tag->setDefault('password', '');
          $this->flash->success('You have registered successfully, you can login now.');

          return $this->dispatcher->forward([
            "controller" => "account",
            "action"     => "login",
          ]);
        }
      }

      $this->view->form = $form;
      }
    }

    /**
     * Logging in a user
     */
    public function loginAction()
    {
      if ($this->request->isPost()) {
        $username = $this->request->getPost('username', 'alphanum');
        $password = $this->request->getPost('password');

        $user = Users::findFirst([
          "username = :username: AND verified = 1",
          'bind' => ['username' => $username]
        ]);

        if ($user != false && password_verify($password, $user->password)) {
          if($user->banned) {
            $this->flash->error('Your account has been banned');
            return;
          }

          $this->_registerSession($user);
          $this->flash->success('Welcome ' . $user->name);

          return $this->dispatcher->forward([
            "controller" => "dashboard",
            "action"     => "index",
          ]);
        }

        $this->flash->error('Wrong email/password');
      }
    }

    /**
     * Terminate the session from a user
     */
    public function logoutAction()
    {
      $this->session->remove('auth');
      $this->flash->success('You have successfully logged-out');

      return $this->dispatcher->forward([
        "controller" => "account",
        "action"     => "login",
      ]);
    }

    /**
     * Recover the password via email
     */
    public function recoverAction()
    {
    }

}
