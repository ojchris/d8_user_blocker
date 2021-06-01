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

//autocomplete stores uid values of the username selected
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
   /* if (mb_strlen($form_state->getValue('message')) < 10) {
      $form_state->setErrorByName('name', $this->t('Message should be at least 10 characters.'));
    }
    */

    parent::validateForm($form, $form_state);

      $userid = $form_state->getValue('userid');
      $user = \Drupal\user\Entity\User::load($userid);
    
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
    $this->messenger()->addStatus($this->t('The message has been sent.'));
    $form_state->setRedirect('<front>');
    */

    
    $user_id = $form_state->getValue('userid');
    $user = User::load($user_id);
    $user->block();
    $user->save();
    $this->messenger()->addMessage($this->t('User %username has been blocked.', ['%username' => $user->getAccountName()]));


  }

}
