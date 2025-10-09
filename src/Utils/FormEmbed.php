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
  /**
   * @deprecated
   * @see FormEmbedType::Inline
   */
  public const TYPE_INLINE = FormEmbedType::Inline->value;
  /**
   * @deprecated
   * @see FormEmbedType::Modal
   */
  public const TYPE_MODAL = FormEmbedType::Modal->value;

  /**
   * @deprecated
   * @see FormEmbedModalType::Popup
   */
  public const MODAL_POPUP = FormEmbedModalType::Popup->value;
  /**
   * @deprecated
   * @see FormEmbedModalType::Slider
   */
  public const MODAL_SLIDER = FormEmbedModalType::Slider->value;
  /**
   * @deprecated
   * @see FormEmbedModalType::Sidetab
   */
  public const MODAL_SIDETAB = FormEmbedModalType::Sidetab->value;
  /**
   * @deprecated
   * @see FormEmbedModalType::Popover
   */
  public const MODAL_POPOVER = FormEmbedModalType::Popover->value;

  private const LIB_URL = 'https://embed.typeform.com/next/embed.js';
  private const OPTION_PREFIX = 'data-tf-';

  private readonly string $formId;
  private readonly FormEmbedType $type;
  private FormEmbedModalType $modalType = FormEmbedModalType::Popup;
  private bool $loadLib = true;
  private bool $loadLibAsync = true;
  private bool $seamless = false;
  private string $label = 'Open Form';
  /** @psalm-var array<string,Option|null> */
  private array $options = [];
  /** @psalm-var HiddenFields */
  private array $hiddenFields = [];

  public function __construct(string|Form|FormStub $form, FormEmbedType|string $type)
  {
    // Convert legacy string to enum case
    if (\is_string($type)) {
      $type = FormEmbedType::from($type);
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
    if ($this->type !== FormEmbedType::Inline) {
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
    if ($this->type !== FormEmbedType::Modal) {
      throw new \BadMethodCallException('Only modal embeds can have labels');
    }
    $this->label = $label;
    return $this;
  }

  /**
   * @return $this
   */
  public function setModalType(string|FormEmbedModalType $modalType): self
  {
    // Convert legacy string to enum case
    if (\is_string($modalType)) {
      $modalType = FormEmbedModalType::from($modalType);
    }

    if ($this->type !== FormEmbedType::Modal) {
      throw new \BadMethodCallException('Only modal embeds can have modal types');
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
  public function setOption(string $option, mixed $value): self
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
  public function setHiddenField(string $field, string $value): self
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
      case FormEmbedType::Inline:
        /** @psalm-var array<string,Option|null> $options */
        $options = array_merge(['widget' => $this->formId], $options);
        if ($this->seamless) {
          $options['hideHeaders'] ??= true;
          $options['hideFooter'] ??= true;
          $options['opacity'] ??= 0;
        }
        break;

      case FormEmbedType::Modal:
        /** @psalm-var array<string,Option|null> $options */
        $options = array_merge([$this->modalType->value => $this->formId], $options);
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

    $html = match ($this->type) {
      FormEmbedType::Inline => '<div ' . $attrs . '></div>',
      FormEmbedType::Modal => '<button ' . $attrs . '>' . self::e($this->label) . '</button>',
    };

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
   */
  private static function e(string|int|float $value): string
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
  private static function getOptionAttrValue(mixed $value): ?string
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
