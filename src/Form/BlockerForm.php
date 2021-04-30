<?php

namespace Drupal\user_blocker\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides a User Blocker form.
 */
class BlockerForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'user_blocker_blocker';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

   /* $form['message'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Message'),
      '#required' => TRUE,
    ];

    $form['actions'] = [
      '#type' => 'actions',
    ];
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Send'),
    ];

    return $form;
    */

    $form['username'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Username'),
      '#description' => $this->t('Enter the username of the user you want to block.'),
      '#maxlength' => 64,
      '#size' => 64,
      '#weight' => '0',
    ];
    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Submit'),
    ];

    return $form;

  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
   /* if (mb_strlen($form_state->getValue('message')) < 10) {
      $form_state->setErrorByName('name', $this->t('Message should be at least 10 characters.'));
    }
    */

    parent::validateForm($form, $form_state);

      $username = $form_state->getValue('username');
      $user = user_load_by_name($username);
      if (empty($user)) {
        $form_state->setError(
          $form['username'],
          $this->t('User %username was not found.', ['%username' => $username])
        );
      }
      else {
        $current_user = \Drupal::currentUser();
        if ($user->id() == $current_user->id()) {
          $form_state->setError(
            $form['username'],
            $this->t('You cannot block your own account.')
          );
        }
      }
    
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    /*
    $this->messenger()->addStatus($this->t('The message has been sent.'));
    $form_state->setRedirect('<front>');
    */

    
    $username = $form_state->getValue('username');
    $user = user_load_by_name($username);
    $user->block();
    $user->save();
    $this->messenger()->addMessage($this->t('User %username has been blocked.', ['%username' => $user->getAccountName()]));


  }

}
