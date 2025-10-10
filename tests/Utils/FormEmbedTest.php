<?php
declare(strict_types=1);

namespace AdamAveray\Typeform\Tests\Utils;

use AdamAveray\Typeform\Models\Forms\Form;
use AdamAveray\Typeform\Models\Forms\FormStub;
use AdamAveray\Typeform\Models\Model;
use AdamAveray\Typeform\Tests\TestCase;
use AdamAveray\Typeform\Utils\FormEmbed;
use AdamAveray\Typeform\Utils\FormEmbedModalType;
use AdamAveray\Typeform\Utils\FormEmbedType;

/**
 * @coversDefaultClass FormEmbed
 */
class FormEmbedTest extends TestCase
{
  /**
   * @covers ::getLibHtml
   * @covers ::setLoadLib
   * @dataProvider getLibHtmlDataProvider
   */
  public function testGetLibHtml(string $expected, bool $loadLib, bool $async): void
  {
    $embed = new FormEmbed('123', FormEmbedType::Modal);
    $embed->setLoadLib($loadLib, $async);
    $this->assertEquals($expected, $embed->getLibHtml(), 'The correct lib HTML should be generated');
  }

  public function getLibHtmlDataProvider(): iterable
  {
    /** @var string $libUrl */
    $libUrl = self::getConst(FormEmbed::class, 'LIB_URL');
    yield 'Sync' => ['<script src="' . self::e($libUrl) . '"></script>', true, false];
    yield 'Async' => ['<script src="' . self::e($libUrl) . '" async defer></script>', true, true];
    yield 'Disabled' => ['<script src="' . self::e($libUrl) . '"></script>', false, false];
  }

  /**
   * @dataProvider generationDataProvider
   * @param (callable(FormEmbed):void)|null $configurator
   */
  public function testGeneration(
    string $expected,
    string $formId,
    FormEmbedType|string $type,
    ?callable $configurator = null,
  ): void {
    // Strip expected newlines & indentation
    $expected = str_replace("\n", '', preg_replace('~\n +~', ' ', $expected));

    $this->assertEmbedHtmlForAllTypes($expected, $formId, function (Form|FormStub|string $form) use (
      $type,
      $configurator,
    ): FormEmbed {
      $embed = new FormEmbed($form, $type);
      if ($configurator !== null) {
        $configurator($embed);
      }
      return $embed;
    });
  }

  public function generationDataProvider(): iterable
  {
    $formId = '>>form-id<<';

    foreach ($this->getInlineGenerationCases($formId) as $key => $case) {
      yield 'Inline: ' . $key => [
        'expected' => $case[0],
        'formId' => $formId,
        'type' => FormEmbedType::Inline,
        'generator' => $case[1] ?? null,
      ];
      /** @psalm-suppress DeprecatedConstant Testing deprecated behaviour. */
      yield 'Inline (Legacy): ' . $key => [
        'expected' => $case[0],
        'formId' => $formId,
        'type' => FormEmbed::TYPE_INLINE,
        'generator' => $case[1] ?? null,
      ];
    }

    foreach ($this->getModalGenerationCases($formId) as $key => $case) {
      yield 'Modal: ' . $key => [
        'expected' => $case[0],
        'formId' => $formId,
        'type' => FormEmbedType::Modal,
        'generator' => $case[1] ?? null,
      ];
      /** @psalm-suppress DeprecatedConstant Testing deprecated behaviour. */
      yield 'Modal (Legacy): ' . $key => [
        'expected' => $case[0],
        'formId' => $formId,
        'type' => FormEmbed::TYPE_MODAL,
        'generator' => $case[1] ?? null,
      ];
    }
  }

  /** @return iterable<string, array{ 0: string, 1?: (callable(FormEmbed):void)|null }> */
  private function getInlineGenerationCases(string $formId): iterable
  {
    $formIdSafe = self::e($formId);
    $lib = (new FormEmbed($formId, FormEmbedType::Inline))->getLibHtml();
    $libSync = (new FormEmbed($formId, FormEmbedType::Inline))->setLoadLib(true, false)->getLibHtml();

    yield 'Default' => [
      <<<HTML
      <div data-tf-widget="{$formIdSafe}"></div>
      {$lib}
      HTML
    ,
    ];

    yield 'No lib' => [
      <<<HTML
      <div data-tf-widget="{$formIdSafe}"></div>
      HTML
      ,
      static function (FormEmbed $embed): void {
        $embed->setLoadLib(false);
      },
    ];

    yield 'Sync lib' => [
      <<<HTML
      <div data-tf-widget="{$formIdSafe}"></div>
      {$libSync}
      HTML
      ,
      static function (FormEmbed $embed): void {
        $embed->setLoadLib(true, false);
      },
    ];

    yield 'Customised' => [
      <<<HTML
      <div
        data-tf-widget="{$formIdSafe}"
        data-tf-replacement-option="replaced-value"
        data-tf-true-option
        data-tf-false-option="0"
        data-tf-string-option="&quot;value&quot;"
        data-tf-int-option="123"
        data-tf-list-option="one,two,three"
        data-tf-assoc-option="one=1,two=2"
        data-tf-overridden-option="new-value"
        data-tf-merge-option="merged-value"
        data-tf-hidden="some_field=field value,another_field=field value 2,merged_field=merge value"
        data-tf-hide-headers
        data-tf-hide-footer
        data-tf-opacity="0"
      ></div>
      {$lib}
      HTML
      ,
      static function (FormEmbed $embed): void {
        $embed
          ->setOption('deletedOption', 'test-value')
          ->setOptions(['replacementOption' => 'replaced-value'], false)
          ->setOption('nullOption', null)
          ->setOption('trueOption', true)
          ->setOption('falseOption', false)
          ->setOption('stringOption', '"value"')
          ->setOption('intOption', 123)
          ->setOption('listOption', ['one', 'two', 'three'])
          ->setOption('assocOption', ['one' => 1, 'two' => 2])
          ->setOption('overriddenOption', 'old-value')
          ->setOption('overriddenOption', 'new-value')
          ->setOptions(['mergeOption' => 'merged-value']);

        $embed
          ->setHiddenField('deleted_field', 'deleted')
          ->setHiddenFields(['some_field' => 'field value'], false)
          ->setHiddenField('another_field', 'field value 2')
          ->setHiddenFields(['merged_field' => 'merge value']);

        // Inline-specific
        $embed->setSeamless();
      },
    ];
  }

  /** @return iterable<string, array{ 0: string, 1?: (callable(FormEmbed):void)|null }> */
  private function getModalGenerationCases(string $formId): iterable
  {
    $formIdSafe = self::e($formId);
    $lib = (new FormEmbed($formId, FormEmbedType::Modal))->getLibHtml();
    $libSync = (new FormEmbed($formId, FormEmbedType::Modal))->setLoadLib(true, false)->getLibHtml();

    yield 'Default' => [
      <<<HTML
      <button data-tf-popup="{$formIdSafe}">Open Form</button>
      {$lib}
      HTML
    ,
    ];

    yield 'No lib' => [
      <<<HTML
      <button data-tf-popup="{$formIdSafe}">Open Form</button>
      HTML
      ,
      static function (FormEmbed $embed): void {
        $embed->setLoadLib(false);
      },
    ];

    yield 'Sync lib' => [
      <<<HTML
      <button data-tf-popup="{$formIdSafe}">Open Form</button>
      {$libSync}
      HTML
      ,
      static function (FormEmbed $embed): void {
        $embed->setLoadLib(true, false);
      },
    ];

    yield 'Customised' => [
      <<<HTML
      <button
        data-tf-slider="{$formIdSafe}"
        data-tf-replacement-option="replaced-value"
        data-tf-true-option
        data-tf-false-option="0"
        data-tf-string-option="&quot;value&quot;"
        data-tf-int-option="123"
        data-tf-list-option="one,two,three"
        data-tf-assoc-option="one=1,two=2"
        data-tf-overridden-option="new-value"
        data-tf-merge-option="merged-value"
        data-tf-hidden="some_field=field value,another_field=field value 2,merged_field=merge value"
      >New Label</button>
      {$lib}
      HTML
      ,
      static function (FormEmbed $embed): void {
        $embed
          ->setOption('deletedOption', 'test-value')
          ->setOptions(['replacementOption' => 'replaced-value'], false)
          ->setOption('nullOption', null)
          ->setOption('trueOption', true)
          ->setOption('falseOption', false)
          ->setOption('stringOption', '"value"')
          ->setOption('intOption', 123)
          ->setOption('listOption', ['one', 'two', 'three'])
          ->setOption('assocOption', ['one' => 1, 'two' => 2])
          ->setOption('overriddenOption', 'old-value')
          ->setOption('overriddenOption', 'new-value')
          ->setOptions(['mergeOption' => 'merged-value']);

        $embed
          ->setHiddenField('deleted_field', 'deleted')
          ->setHiddenFields(['some_field' => 'field value'], false)
          ->setHiddenField('another_field', 'field value 2')
          ->setHiddenFields(['merged_field' => 'merge value']);

        // Modal-specific
        $embed->setLabel('New Label')->setModalType(FormEmbedModalType::Slider);
      },
    ];
  }

  /**
   * @param callable(Form|FormStub|string):FormEmbed $generator
   */
  private function assertEmbedHtmlForAllTypes(string $expected, string $formId, callable $generator): void
  {
    self::runForAllFormTypes($formId, function (Form|FormStub|string $form, string $type) use (
      $expected,
      $generator,
    ): void {
      $embed = $generator($form);
      $this->assertEmbedHtml($expected, $embed, 'The correct embed HTML should be generated for ' . $type . ' forms');
    });
  }

  /**
   * @param callable(Form|FormStub|string, string):void $fn
   */
  private static function runForAllFormTypes(string $formId, callable $fn): void
  {
    $formTypes = [
      'string' => $formId,
      Form::class => self::getPseudoForm($formId, Form::class),
      FormStub::class => self::getPseudoForm($formId, FormStub::class),
    ];
    foreach ($formTypes as $type => $form) {
      $fn($form, $type);
    }
  }

  /**
   * @covers ::setSeamless
   */
  public function testSetSeamlessOnModal(): void
  {
    $this->expectException(\BadMethodCallException::class);
    (new FormEmbed('abc', FormEmbedType::Modal))->setSeamless();
  }

  /**
   * @covers ::setLabel
   */
  public function testSetLabelOnInline(): void
  {
    $this->expectException(\BadMethodCallException::class);
    (new FormEmbed('abc', FormEmbedType::Inline))->setLabel('Label');
  }

  /**
   * @covers ::setModalType
   */
  public function testSetModalTypeOnInline(): void
  {
    $this->expectException(\BadMethodCallException::class);
    (new FormEmbed('abc', FormEmbedType::Inline))->setModalType(FormEmbedModalType::Popover);
  }

  /**
   * @covers ::setModalType
   */
  public function testSetInvalidModalType(): void
  {
    $this->expectException(\ValueError::class);
    (new FormEmbed('abc', FormEmbedType::Modal))->setModalType('not-a-modal-type');
  }

  /**
   * @covers ::__construct
   */
  public function testInvalidType(): void
  {
    $this->expectException(\ValueError::class);
    new FormEmbed('abc', 'unknown-type');
  }

  private function assertEmbedHtml(string $expected, FormEmbed $embed, ?string $message = null): void
  {
    $this->assertEquals($expected, $embed->getHtml(), $message ?? 'The correct embed HTML should be generated');
    $this->assertEquals(
      $expected,
      (string) $embed,
      $message ?? 'The correct embed HTML should be generated when converting to string',
    );
  }

  private static function e(string $value): string
  {
    return htmlspecialchars($value, \ENT_QUOTES | \ENT_HTML5);
  }

  /**
   * @param class-string<Form>|class-string<FormStub> $className
   * @return Form|FormStub
   */
  private static function getPseudoForm(string $id, string $className): Model
  {
    $reflectionClass = new \ReflectionClass($className);
    $form = $reflectionClass->newInstanceWithoutConstructor();
    self::setReadonlyProperties($form, 'id', $id);
    return $form;
  }
}
