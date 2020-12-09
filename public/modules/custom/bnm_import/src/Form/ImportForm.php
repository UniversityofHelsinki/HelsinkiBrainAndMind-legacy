<?php

namespace Drupal\bnm_import\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\file\Entity\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ImportForm extends FormBase {

  public function getFormId(): string {
    return 'bnm_import_form';
  }

  public function buildForm(array $form, FormStateInterface $form_state): array {
    $form['import'] = [
      '#type' => 'file',
      '#title' => 'Import from csv',
      '#upload_location' => 'managed_file://csv',
    ];

    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Import'),
      '#button_type' => 'primary',
    ];

    return $form;
  }

  public function validateForm(array &$form, FormStateInterface $form_state) {
    $csv_handler = \Drupal::service('bnm_import.csv_validator');
    $all_files = $this->getRequest()->files->get('files', []);
    /** @var UploadedFile $file */
    $file = $all_files['import'];

    if (!$file) {
      $form_state->setErrorByName('import', 'Set the file for the import first.');
      return;
    }

    if (!$file->getClientOriginalExtension() || $file->getClientOriginalExtension() !== 'csv') {
      $form_state->setErrorByName('import', 'Unsupported file extension detected. You can only import from .csv file.');
      return;
    }
    try{
      $fh = fopen($file->getRealPath(), 'r');
      $errors = $csv_handler->validateImportData($fh);
      fclose($fh);
    }
    catch(\Exception $exception){
      $form_state->setErrorByName('import', $exception->getMessage());
    }


    if($errors){
      $name = 'import';
      $form_state->setErrorByName($name, $errors[0]);
    }
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {
    $csv_handler = \Drupal::service('bnm_import.csv_validator');
    $all_files = $this->getRequest()->files->get('files', []);
    /** @var UploadedFile $file */
    $file = $all_files['import'];
    try {
      $fh = fopen($file->getRealPath(), 'r');
      $content = $csv_handler->createContent($fh);
      fclose($fh);
    }
    catch(\Exception $exception){
      fclose($fh);
      $form_state->setErrorByName('import', $exception->getMessage());
    }

    $node_count = count($content['nodes']);
    $term_count = count($content['terms']);

    \Drupal::messenger()->addMessage("Created $node_count new research groups and created $term_count new keywords in the process.");
  }

}
