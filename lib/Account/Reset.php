<?php declare(strict_types = 1);

/**
 * PHP version 7.4
 *
 * @category  PHP
 * @package   Madsoft\Library\Account
 * @author    Gyula Madarasz <gyula.madarasz@gmail.com>
 * @copyright 2020 Gyula Madarasz
 * @license   Copyright (c) All rights reserved.
 * @link      this
 */

namespace Madsoft\Library\Account;

use Madsoft\Library\Config;
use Madsoft\Library\Crud;
use Madsoft\Library\Mailer;
use Madsoft\Library\Merger;
use Madsoft\Library\Params;
use Madsoft\Library\Template;

/**
 * Reset
 *
 * @category  PHP
 * @package   Madsoft\Library\Account
 * @author    Gyula Madarasz <gyula.madarasz@gmail.com>
 * @copyright 2020 Gyula Madarasz
 * @license   Copyright (c) All rights reserved.
 * @link      this
 */
class Reset extends Account
{
    protected Crud $crud;
    protected Params $params;
    protected Template $template;
    protected Validator $validator;
    protected Mailer $mailer;
    protected Config $config;

    /**
     * Method __construct
     *
     * @param Template  $template  template
     * @param Merger    $merger    merger
     * @param Crud      $crud      crud
     * @param Params    $params    params
     * @param Validator $validator validator
     * @param Mailer    $mailer    mailer
     * @param Config    $config    config
     */
    public function __construct(
        Template $template,
        Merger $merger,
        Crud $crud,
        Params $params,
        Validator $validator,
        Mailer $mailer,
        Config $config
    ) {
        parent::__construct($template, $merger);
        $this->crud = $crud;
        $this->params = $params;
        $this->validator = $validator;
        $this->mailer = $mailer;
        $this->config = $config;
    }
    
    /**
     * Method reset
     *
     * @return string
     */
    public function reset(): string
    {
        $token = $this->params->get('token', '');
        if ($token) {
            $user = $this->crud->get('user', ['id'], ['token' => $token]);
            if (!$user->get('id')) {
                return $this->getErrorResponse('reset.phtml', 'Invalid token');
            }
            return $this->getResponse('change.phtml', ['token' => $token]);
        }
        return $this->getResponse('reset.phtml');
    }
    
    /**
     * Method doReset
     *
     * @return string
     */
    public function doReset(): string
    {
        $errors = $this->validator->validateReset($this->params);
        if ($errors) {
            return $this->getErrorResponse(
                'reset.phtml',
                'Reset password failed',
                $errors
            );
        }
        
        $email = (string)$this->params->get('email');
        $user = $this->crud->get('user', ['email'], ['email' => $email]);
        if ($user->get('email') !== $email) {
            return $this->getErrorResponse(
                'reset.phtml',
                'Email address not found'
            );
        }
        
        $token = $this->generateToken();
        if (!$this->crud->set('user', ['token' => $token], ['email' => $email])) {
            return $this->getErrorResponse(
                'reset.phtml',
                'Token is not updated'
            );
        }
        
        if (!$this->sendResetEmail($email, $token)) {
            return $this->getErrorResponse(
                'reset.phtml',
                'Email sending failed'
            );
        }
        
        return $this->getSuccesResponse(
            'reset.phtml',
            'Password reset request email sent'
        );
    }
    
    /**
     * Method sendResetEmail
     *
     * @param string $email email
     * @param string $token token
     *
     * @return bool
     */
    protected function sendResetEmail(string $email, string $token): bool
    {
        $message = $this->template->process(
            $this::TPL_PATH . 'emails/reset.phtml',
            [
                'base' => $this->config->get('Site')->get('base'),
                'token' => $token,
            ]
        );
        return $this->mailer->send(
            $email,
            'Pasword reset requested',
            $message
        );
    }
}
