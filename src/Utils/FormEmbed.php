<?php
declare(strict_types=1);

namespace AdamAveray\Typeform\Utils;

use AdamAveray\Typeform\Models\Forms\Form;
use AdamAveray\Typeform\Models\Forms\FormStub;

/**
 * @psalm-type Option = bool|string|int|float|list<string|int|float>|array<string,string|int|float>
 * @psalm-type HiddenFields = array<string,string>
 */
class FormEmbed
{
  public const TYPE_INLINE = 'inline';
  public const TYPE_MODAL = 'modal';
  public const MODAL_POPUP = 'popup';
  public const MODAL_SLIDER = 'slider';
  public const MODAL_SIDETAB = 'sidetab';
  public const MODAL_POPOVER = 'popover';
  private const LIB_URL = 'https://embed.typeform.com/next/embed.js';
  private const OPTION_PREFIX = 'data-tf-';

  /** @psalm-var self::TYPE_*[] $types */
  private static array $types = [self::TYPE_INLINE, self::TYPE_MODAL];
  /** @psalm-var self::MODAL_*[] $modalTypes */
  private static array $modalTypes = [self::MODAL_POPUP, self::MODAL_SLIDER, self::MODAL_SIDETAB, self::MODAL_POPOVER];

  /** @readonly */
  private string $formId;
  /**
   * @readonly
   * @psalm-var self::TYPE_* $type
   */
  private string $type;
  /** @psalm-var self::MODAL_* $modalType */
  private string $modalType = self::MODAL_POPUP;
  private bool $loadLib = true;
  private bool $loadLibAsync = true;
  private bool $seamless = false;
  private string $label = 'Open Form';
  /** @psalm-var array<string,Option|null> */
  private array $options = [];
  /** @psalm-var HiddenFields */
  private array $hiddenFields = [];

  /**
   * @param string|Form|FormStub $form
   * @psalm-param self::TYPE_* $type
   */
  public function __construct($form, string $type)
  {
    if (!\in_array($type, self::$types, true)) {
      throw new \InvalidArgumentException('Invalid embed type');
    }
    $this->formId = $form instanceof Form || $form instanceof FormStub ? $form->id : $form;
    $this->type = $type;
  }

  /**
   * @param bool $loadLib Whether to load the Typeform JS library on the page
   * @param bool $async If $loadLib is true, whether to load the library asynchronously
   * @return $this
   */
  public function setLoadLib(bool $loadLib, bool $async = true): self
  {
    $this->loadLib = $loadLib;
    $this->loadLibAsync = $async;
    return $this;
  }

  /**
   * @param bool $seamless Whether to remove the header, footer and background from the form embed
   * @return $this
   */
  public function setSeamless(bool $seamless = true): self
  {
    if ($this->type !== self::TYPE_INLINE) {
      throw new \BadMethodCallException('Only inline embeds can be seamless');
    }
    $this->seamless = $seamless;
    return $this;
  }

  /**
   * @param string $label The label displayed on the button to open the modal
   * @return $this
   */
  public function setLabel(string $label): self
  {
    if ($this->type !== self::TYPE_MODAL) {
      throw new \BadMethodCallException('Only modal embeds can have labels');
    }
    $this->label = $label;
    return $this;
  }

  /**
   * @psalm-param self::MODAL_* $modalType
   * @return $this
   */
  public function setModalType(string $modalType): self
  {
    if ($this->type !== self::TYPE_MODAL) {
      throw new \BadMethodCallException('Only modal embeds can have modal types');
    }
    if (!in_array($modalType, self::$modalTypes, true)) {
      throw new \InvalidArgumentException('Invalid modal type');
    }
    $this->modalType = $modalType;
    return $this;
  }

  /**
   * @param string $option A camelCased embed option name
   * @param mixed $value
   * @psalm-param Option|null $value
   * @return $this
   * @link https://developer.typeform.com/embed/configuration/#options-in-plain-html-embed
   * @see setOptions
   */
  public function setOption(string $option, $value): self
  {
    if ($value !== null) {
      $this->options[$option] = $value;
    }
    return $this;
  }

  /**
   * @param array $options
   * @psalm-param array<string,Option|null> $options
   * @param bool $merge If true, will preserve existing options, if false will remove any previously-set options
   * @return $this
   * @link https://developer.typeform.com/embed/configuration/#options-in-plain-html-embed
   * @see setOption
   */
  public function setOptions(array $options, bool $merge = true): self
  {
    $this->options = $merge ? array_merge($this->options, $options) : $options;
    return $this;
  }

  /**
   * @param string $field
   * @param string $value
   * @return $this
   * @see setHiddenFields
   * @link https://developer.typeform.com/embed/hidden-fields/#pass-values-from-host-page-url
   */
  public function setHiddenField($field, string $value): self
  {
    $this->hiddenFields[$field] = $value;
    return $this;
  }

  /**
   * @param array $fields
   * @psalm-param HiddenFields $fields
   * @param bool $merge If true, will preserve existing fields, if false will remove any previously-set fields
   * @return $this
   * @see setHiddenField
   * @link https://developer.typeform.com/embed/hidden-fields/#pass-values-from-host-page-url
   */
  public function setHiddenFields(array $fields, bool $merge = true): self
  {
    $this->hiddenFields = $merge ? array_merge($this->hiddenFields, $fields) : $fields;
    return $this;
  }

  /**
   * @return array The full Typeform SDK options for the form
   *
   * @psalm-return array<string,Option>
   */
  public function getFullOptions(): array
  {
    $options = $this->options;
    if ($this->hiddenFields !== []) {
      $options['hidden'] ??= $this->hiddenFields;
    }

    switch ($this->type) {
      case self::TYPE_INLINE:
        /** @psalm-var array<string,Option|null> $options */
        $options = array_merge(['widget' => $this->formId], $options);
        if ($this->seamless) {
          $options['hideHeaders'] ??= true;
          $options['hideFooter'] ??= true;
          $options['opacity'] ??= 0;
        }
        break;

      case self::TYPE_MODAL:
        /** @psalm-var array<string,Option|null> $options */
        $options = array_merge([$this->modalType => $this->formId], $options);
        break;
    }

    // Strip nulls
    return array_filter($options, static fn($value): bool => $value !== null);
  }

  /**
   * @return string The HTML data attributes for the form which will be automatically interpreted by the JavaScript SDK
   */
  public function getHtmlAttrs(): string
  {
    return self::buildOptions($this->getFullOptions());
  }

  /**
   * @return string The full HTML code for the embed
   */
  public function getHtml(): string
  {
    $attrs = $this->getHtmlAttrs();

    switch ($this->type) {
      case self::TYPE_INLINE:
        $html = '<div ' . $attrs . '></div>';
        break;

      case self::TYPE_MODAL:
        /** @psalm-var array<string,Option> $options */
        $html = '<button ' . $attrs . '>' . self::e($this->label) . '</button>';
        break;
    }

    return $html . ($this->loadLib ? $this->getLibHtml() : '');
  }

  /**
   * @return string The HTML script tag to load the Typeform HTML
   */
  public function getLibHtml(): string
  {
    return '<script src="' . self::e(self::LIB_URL) . '"' . ($this->loadLibAsync ? ' async defer' : '') . '></script>';
  }

  public function __toString(): string
  {
    return $this->getHtml();
  }

  /**
   * HTML-escapes a value for safe insertion into a HTML document
   *
   * @param string|int|float $value
   */
  private static function e($value): string
  {
    return htmlspecialchars((string) $value, \ENT_QUOTES | \ENT_HTML5);
  }

  /**
   * @psalm-param array<string,Option> $options
   */
  private static function buildOptions(array $options): string
  {
    $strings = [];
    foreach ($options as $option => $value) {
      $attrName = self::getOptionAttrName($option);
      $attrValue = self::getOptionAttrValue($value);
      $strings[] = self::e($attrName) . ($attrValue === null ? '' : '="' . self::e($attrValue) . '"');
    }
    return implode(' ', $strings);
  }

  /**
   * @param string $option The camelCased option name
   * @return string The HTML data attribute for the option
   */
  private static function getOptionAttrName(string $option): string
  {
    return self::OPTION_PREFIX . strtolower(preg_replace('~(?<!^)[A-Z]~', '-$0', $option));
  }

  /**
   * @param mixed $value The option value
   * @psalm-param Option $value
   * @return string|null A serialised string representation of $value, or null if it should be omitted
   * @link https://developer.typeform.com/embed/configuration/#options-in-plain-html-embed
   */
  private static function getOptionAttrValue($value): ?string
  {
    if ($value === true) {
      return null;
    }

    if ($value === false) {
      return '0';
    }

    if (is_array($value)) {
      $isList = $value === [] || $value === array_values($value);
      if (!$isList) {
        $value = self::formatAssociativeArray($value);
      }
      return implode(',', $value);
    }

    return (string) $value;
  }

  private static function formatAssociativeArray(array $values): array
  {
    $list = [];
    foreach ($values as $key => $value) {
      $list[] = $key . '=' . $value;
    }
    return $list;
  }
}
