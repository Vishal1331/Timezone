<?php
/**
 * @file
 * Contains Drupal\timezone\Form\TimezoneConfigForm.
 */
namespace Drupal\timezone\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Entity\EntityTypeManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Cache\CacheTagsInvalidator;

class TimezoneConfigForm extends ConfigFormBase {
  /**
   * @var EntityTypeManager $entityTypeManager
   * @var CacheTagsInvalidator $cacheTagsInvalidator
   */
  protected $entityTypeManager;
  protected $cacheTagsInvalidator;

  /**
   * {@inheritdoc}
   *  Gets the configuration name.
   */
  protected function getEditableConfigNames() {
    return [
      'config.timezone_config_form',
    ];
  }

  /**
   * Class constructor.
   */
  public function __construct(EntityTypeManager $entityTypeManager, CacheTagsInvalidator $cacheTagsInvalidator) {
    $this->entityTypeManager = $entityTypeManager;
    $this->cacheTagsInvalidator = $cacheTagsInvalidator;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    // Instantiates this form class.
    return new static(
      // Load the service required to construct this class.
      $container->get('entity_type.manager'),
      $container->get('cache_tags.invalidator'),
    );
  }

  /**  
   * {@inheritdoc}  
   * Returns the form’s unique ID.
   */  
  public function getFormId() {
    return 'timezone_config_form';
  }

  /**  
   * {@inheritdoc}  
   */  
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('config.timezone_config_form');  
    $vid = 'timezone';
    $terms =$this->entityTypeManager->getStorage('taxonomy_term')->loadTree($vid);
    foreach ($terms as $term) {
      $term_data[$term->name] = $term->name;
    }

    // Country textfield.
    $form['country'] = [  
      '#type' => 'textfield',  
      '#title' => $this->t('Country'), 
      '#required' => TRUE, 
      '#default_value' => $config->get('timezone_config_country'),  
    ];  
    
    // City textfield.
    $form['city'] = [
      '#type' => 'textfield',  
      '#title' => $this->t('City'), 
      '#required' => TRUE, 
      '#default_value' => $config->get('timezone_config_city'),  
    ];  

    // Timezone field.
    $form['timezone'] = [ 
      '#type' => 'select',  
      '#title' => $this->t('Timezone'), 
      '#options' => $term_data,
      '#required' => TRUE, 
      '#default_value' => $config->get('timezone_config_timezone'),  
      ];  

    return parent::buildForm($form, $form_state);
  }

  /**  
   * {@inheritdoc}  
   */  
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);  
    
    // Set values.
    $this->config('config.timezone_config_form')
      ->set('timezone_config_country', $form_state->getValue('country'))
      ->set('timezone_config_city', $form_state->getValue('city'))
      ->set('timezone_config_timezone', $form_state->getValue('timezone'))  
      ->save();
     
     // Invalidated 'custom_timezone_config_tag' cache tag.
     $this->cacheTagsInvalidator->invalidateTags(['custom_timezone_config_tag']);
  }  

}  