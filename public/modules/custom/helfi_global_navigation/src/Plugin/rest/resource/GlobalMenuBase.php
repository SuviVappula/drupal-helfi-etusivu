<?php

declare(strict_types = 1);

namespace Drupal\helfi_global_navigation\Plugin\rest\resource;

use Drupal\Core\Language\LanguageInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\helfi_global_navigation\Entity\GlobalMenu as GlobalMenuEntity;
use Drupal\helfi_global_navigation\Entity\Storage\GlobalMenuStorage;
use Drupal\rest\Plugin\ResourceBase;
use Drupal\rest\Plugin\rest\resource\EntityResourceValidationTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

/**
 * A base class for global menu resources.
 */
abstract class GlobalMenuBase extends ResourceBase {

  use EntityResourceValidationTrait;

  /**
   * The language manager.
   *
   * @var \Drupal\Core\Language\LanguageManagerInterface
   */
  protected LanguageManagerInterface $languageManager;

  /**
   * The entity storage.
   *
   * @var \Drupal\helfi_global_navigation\Entity\Storage\GlobalMenuStorage
   */
  protected GlobalMenuStorage $storage;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) : self {
    $instance = parent::create(
      $container,
      $configuration,
      $plugin_id,
      $plugin_definition
    );
    $instance->languageManager = $container->get('language_manager');
    $instance->storage = $container->get('entity_type.manager')->getStorage('global_menu');

    return $instance;
  }

  /**
   * Asserts entity permissions.
   *
   * @param \Drupal\helfi_global_navigation\Entity\GlobalMenu $entity
   *   The entity to check.
   * @param string $operation
   *   The entity operation.
   *
   * @return $this
   *   The self.
   */
  protected function assertPermission(GlobalMenuEntity $entity, string $operation) : static {
    $access = $entity->access($operation, return_as_object: TRUE);

    if (!$access->isAllowed()) {
      throw new AccessDeniedHttpException("You are not authorized to {$operation} this {$entity->getEntityTypeId()} entity");
    }
    return $this;
  }

  /**
   * Gets the current language ID.
   *
   * @return string
   *   The language ID.
   */
  protected function getCurrentLanguageId() : string {
    return $this->languageManager
      ->getCurrentLanguage(LanguageInterface::TYPE_CONTENT)
      ->getId();
  }

}
