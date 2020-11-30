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
      '#upload_location' => 'managed_file://certfiles',
      #'#required' => true,
      '#upload_validators' => [
        'file_validate_extensions' => ['csv'],
        'validate_csv_file' => [],
      ],
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
      $csv_handler->createContent();
      close($fh);
    }
    catch(\Exception $exception){
      $form_state->setErrorByName('import', $exception->getMessage());
    }
  }

}
