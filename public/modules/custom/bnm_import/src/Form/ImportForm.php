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

}
