<?php

namespace Drupal\user_blocker\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\user\Entity\User;

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
    /*
    // This is the text field version.
     $form['username'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Username'),
      '#description' => $this->t('Enter the username of the user you want to block.'),
      '#maxlength' => 64,
      '#size' => 64,
      '#weight' => '0',
    ];
    */

    // This is the autocomplete version
    //autocomplete stores uid values of the username selected.
    $form['userid'] = [
      '#type' => 'entity_autocomplete',
      '#target_type' => 'user',
      '#title' => $this->t('Username'),
      '#description' => $this->t('Enter the username of the user you want to block.'),
      '#required' => 'true',
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
    parent::validateForm($form, $form_state);

    /*
    //this is the textfield dependent version
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
      */

      //this is the autocomplete dependent version
      $userid = $form_state->getValue('userid');
    
        $current_user = \Drupal::currentUser();
        if ($userid == $current_user->id()) {
          $form_state->setError(
            $form['userid'],
            $this->t('You cannot block your own account.')
          );
        }
      
    
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    /*
    //this is the textfield & username dependent version
    $username = $form_state->getValue('username');
    $user = user_load_by_name($username);
    */

    //this is the autocomplete & userID dependent version
    $user_id = $form_state->getValue('userid');
    $user = User::load($user_id);
    
    $user->block();
    $user->save();
    $this->messenger()->addMessage($this->t('User %username has been blocked.', ['%username' => $user->getAccountName()]));


  }

}
