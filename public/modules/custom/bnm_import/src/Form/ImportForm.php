<?php

namespace Drupal\bnm_import\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\node\Entity\Node;
use Symfony\Component\HttpFoundation\Response;

/**
 *
 */
class ImportForm extends FormBase {

  /**
   *
   */
  public function getFormId(): string {
    return 'bnm_import_form';
  }

  /**
   *
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {
    $form['import'] = [
      '#type' => 'file',
      '#title' => 'Import from csv',
      '#upload_location' => 'managed_file://csv',
    ];

    $form['submit'] = [
      '#type' => 'submit',
      '#name' => 'submit',
      '#value' => $this->t('Import'),
      '#button_type' => 'primary',
    ];

    $form['actions']['form_2_submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Export CSV'),
      '#name' => 'export_csv',
      '#submit' => ['::createCsv'],
    ];

    return $form;
  }

  /**
   *
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {

    if ($form_state->getTriggeringElement()['#name'] === 'export_csv') {
      return;
    }

    $csv_handler = \Drupal::service('bnm_import.csv_validator');
    $all_files = $this->getRequest()->files->get('files', []);
    /** @var \Symfony\Component\HttpFoundation\File\UploadedFile $file */
    $file = $all_files['import'];

    if (!$file) {
      $form_state->setErrorByName('import', 'Set the file for the import first.');
      return;
    }

    if (!$file->getClientOriginalExtension() || $file->getClientOriginalExtension() !== 'csv') {
      $form_state->setErrorByName('import', 'Unsupported file extension detected. You can only import from .csv file.');
      return;
    }
    try {
      $fh = fopen($file->getRealPath(), 'r');
      $errors = $csv_handler->validateImportData($fh);
      fclose($fh);
    }
    catch (\Exception $exception) {
      $form_state->setErrorByName('import', $exception->getMessage());
    }

    if ($errors) {
      $name = 'import';
      $form_state->setErrorByName($name, $errors[0]);
    }
  }

  /**
   *
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $csv_handler = \Drupal::service('bnm_import.csv_validator');
    $all_files = $this->getRequest()->files->get('files', []);
    /** @var \Symfony\Component\HttpFoundation\File\UploadedFile $file */
    $file = $all_files['import'];
    try {
      $fh = fopen($file->getRealPath(), 'r');
      $content = $csv_handler->createContent($fh);
      fclose($fh);
      $node_count = count($content['nodes']);
      $term_count = count($content['terms']);
      \Drupal::messenger()->addMessage("Created $node_count new research groups and created $term_count new keywords in the process.");
    }
    catch (\Exception $exception) {
      fclose($fh);
      \Drupal::messenger()->addError("Unexpected error: {$exception->getMessage()}");
    }
  }

  /**
   *
   */
  public function createCsv(array &$form, FormStateInterface $form_state) {
    $csv_creator = \Drupal::service('bnm_import.csv_creator');
    $config = \Drupal::config('bnm_import.group_field_map')->get('field_map');

    $csv_headers = array_merge($config['required'], $config['optional']);
    $nodes = Node::loadMultiple();
    $keywords = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadTree('Keywords');
    $organisations = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadTree('Affiliations');

    try {
      $csv_data = $csv_creator->createCsvData($nodes, $keywords, $organisations, $csv_headers);
      $csv = $csv_creator->createCsvFile($csv_data);

      $response = new Response($csv);
      $response->headers->set('Content-type', 'text/csv');
      $response->headers->set('Content-Disposition', 'attachment; filename=researchgroup_export.csv');
      $form_state->setRebuild();
      $form_state->setResponse($response);
    }
    catch (\Exception $exception) {
      \Drupal::messenger()->addError('Error while creating csv file.');
    }
  }

}
