<?php

namespace Drupal\bnm_searchapi\Plugin\search_api\processor;

use Drupal\Core\Form\FormStateInterface;
use Drupal\search_api\Item\FieldInterface;
use Drupal\search_api\Plugin\PluginFormTrait;
use Drupal\search_api\Plugin\search_api\data_type\value\TextValueInterface;
use Drupal\search_api\Processor\FieldsProcessorPluginBase;
use Drupal\search_api\Utility\DataTypeHelperInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Underscore processor.
 *
 * @SearchApiProcessor(
 *   id = "bnm_underscore_processor",
 *   label = @Translation("BNM - underscore processor"),
 *   description = @Translation("Replace whitespace in fulltext keyword with underscore"),
 *   stages = {
 *     "pre_index_save" = 5,
 *     "preprocess_index" = -15
 *   }
 * )
 */
class UnderscoreProcessor extends FieldsProcessorPluginBase {

  use PluginFormTrait;

  /**
   * The data type helper.
   *
   * @var \Drupal\search_api\Utility\DataTypeHelperInterface|null
   */
  protected $dataTypeHelper;

  /**
   * Retrieves the data type helper.
   *
   * @return \Drupal\search_api\Utility\DataTypeHelperInterface
   *   The data type helper.
   */
  public function getDataTypeHelper() {
    return $this->dataTypeHelper ?: \Drupal::service('search_api.data_type_helper');
  }

  /**
   * Sets the data type helper.
   *
   * @param \Drupal\search_api\Utility\DataTypeHelperInterface $data_type_helper
   *   The new data type helper.
   *
   * @return $this
   */
  public function setDataTypeHelper(DataTypeHelperInterface $data_type_helper) {
    $this->dataTypeHelper = $data_type_helper;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    /** @var static $processor */
    $processor = parent::create($container, $configuration, $plugin_id, $plugin_definition);

    return $processor;
  }

  /**
   * {@inheritdoc}
   */
  public function preIndexSave() {
    parent::preIndexSave();

  }

  /**
   * {@inheritdoc}
   */
  public function preprocessIndexItems(array $items) {
    // Annoyingly, this doc comment is needed for PHPStorm. See
    // http://youtrack.jetbrains.com/issue/WI-23586
    /** @var \Drupal\search_api\Item\ItemInterface $item */
    foreach ($items as $item) {
      foreach ($item->getFields() as $name => $field) {
        if ($this->testField($name, $field) && !empty($field->getValues())) {
          //process words one by one
          foreach (explode(' ', reset($field->getValues())) as $word) {
            $obj = clone $field;
            $obj->setValues([$word]);
            $this->processField($obj);
          }
        }
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    $form = parent::buildConfigurationForm($form, $form_state);
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateConfigurationForm(array &$form, FormStateInterface $form_state) {
    parent::validateConfigurationForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  protected function processField(FieldInterface $field) {
    parent::processField($field);

    foreach ($field->getValues() as $value) {
      if ($value instanceof TextValueInterface) {
        $value->setProperty('underscore');
      }
    }
  }

  /**
   * @param string $value
   * @param string $type
   */
  protected function processFieldValue(&$value, $type) {
     $this->process($value);
  }

  /**
   * {@inheritdoc}
   */
  protected function process(&$value) {
    if (is_string($value)) {
      $value = str_replace(' ', 'qq', $value) . 'q';
    }
  }

}
