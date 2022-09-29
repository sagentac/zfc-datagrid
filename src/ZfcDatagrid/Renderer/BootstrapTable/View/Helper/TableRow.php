<?php
namespace ZfcDatagrid\Renderer\BootstrapTable\View\Helper;

use Laminas\I18n\Translator\TranslatorInterface;
use Laminas\View\Helper\AbstractHelper;
use ZfcDatagrid\Column;
use ZfcDatagrid\Column\Action\AbstractAction;
use function implode;
use function get_class;
use function print_r;
use function array_merge;

/**
 * View Helper.
 */
class TableRow extends AbstractHelper
{
    /** @var TranslatorInterface|null */
    private $translator;

    /**
     * @param null|TranslatorInterface $translator
     *
     * @return self
     */
    public function setTranslator(?TranslatorInterface $translator)
    {
        $this->translator = $translator;

        return $this;
    }

    /**
     * @param string $message
     *
     * @return string
     */
    private function translate(string $message): string
    {
        if (null === $this->translator) {
            return $message;
        }

        return $this->translator->translate($message);
    }

    /**
     * @param array $row
     * @param bool  $open
     *
     * @return string
     */
    private function getTr(array $row, bool $open = true): string
    {
        if ($open !== true) {
            return '</tr>';
        } else {
            if (isset($row['idConcated'])) {
                return '<tr id="' . $row['idConcated'] . '">';
            } else {
                return '<tr>';
            }
        }
    }

    /**
     * @param string $dataValue
     * @param array $attributes
     *
     * @return string
     */
    private function getTd(string $dataValue, array $attributes = []): string
    {
        $attr = [];
        foreach ($attributes as $name => $value) {
            if ($value != '') {
                $attr[] = $name . '="' . $value . '"';
            }
        }

        $attr = implode(' ', $attr);

        return '<td ' . $attr . '>' . $dataValue . '</td>';
    }

    /**
     * @param array          $row
     * @param array          $cols
     * @param AbstractAction $rowClickAction
     * @param array          $rowStyles
     * @param bool           $hasMassActions
     *
     * @throws \Exception
     *
     * @return string
     */
    public function __invoke(
        array $row,
        array $cols,
        AbstractAction $rowClickAction = null,
        array $rowStyles = [],
        bool $hasMassActions = false
    ): string {
        $return = $this->getTr($row);

        if (true === $hasMassActions) {
            $return .= '<td><input type="checkbox" name="massActionSelected[]" value="' . $row['idConcated'] . '" /></td>';
        }

        foreach ($cols as $col) {
            /* @var $col Column\AbstractColumn */
            if (true === $col->isHide()) {
                continue;
            }

            $value = $row[$col->getUniqueId()];

            /*
             * Replace
             */
            if ($col->hasReplaceValues() === true) {
                $replaceValues = $col->getReplaceValues();

                if (is_array($value)) {
                    foreach ($value as &$valueEntry) {
                        if (isset($replaceValues[$valueEntry])) {
                            $valueEntry = $replaceValues[$valueEntry];
                        } elseif ($col->notReplacedGetEmpty() === true) {
                            $valueEntry = '';
                        }
                    }
                } else {
                    if (isset($replaceValues[$value])) {
                        $value = $replaceValues[$value];
                    } elseif ($col->notReplacedGetEmpty() === true) {
                        $value = '';
                    }
                }
            }

            if ($col->isTranslationEnabled() === true) {
                if (is_array($value)) {
                    foreach ($value as &$valueEntry) {
                        if (is_array($valueEntry)) {
                            continue;
                        }
                        $valueEntry = $this->translate($valueEntry);
                    }
                } else {
                    $value = $this->translate($value);
                }
            }

            $cssStyles = [];
            $classes   = [];

            if (true === $col->isHidden()) {
                $classes[] = 'hidden';
            }

            switch (get_class($col->getType())) {
                case Column\Type\Number::class:
                    $cssStyles[] = 'text-align: right';
                    break;

                case Column\Type\PhpArray::class:
                    $value = '<pre>' . print_r($value, true) . '</pre>';
                    break;
            }

            $styles = array_merge($rowStyles, $col->getStyles());
            foreach ($styles as $style) {
                /* @var $style Column\Style\AbstractStyle */
                if ($style->isApply($row) === true) {
                    switch (get_class($style)) {
                        case Column\Style\Bold::class:
                            $cssStyles[] = 'font-weight: bold';
                            break;

                        case Column\Style\Italic::class:
                            $cssStyles[] = 'font-style: italic';
                            break;

                        case Column\Style\Color::class:
                            $cssStyles[] = 'color: #' . $style->getRgbHexString();
                            break;

                        case Column\Style\BackgroundColor::class:
                            $cssStyles[] = 'background-color: #' . $style->getRgbHexString();
                            break;

                        case Column\Style\Align::class:
                            $cssStyles[] = 'text-align: ' . $style->getAlignment();
                            break;

                        case Column\Style\Strikethrough::class:
                            $value = '<s>' . $value . '</s>';
                            break;

                        case Column\Style\CSSClass::class:
                            $classes[] = $style->getClass();
                            break;

                        case Column\Style\Html::class:
                            // do NOTHING! just pass the HTML!
                            break;

                        default:
                            throw new \InvalidArgumentException('Not defined style: "' . get_class($style) . '"');
                            break;
                    }
                }
            }

            if ($col instanceof Column\Action) {
                /* @var $col Column\Action */
                $actions = [];
                foreach ($col->getActions() as $action) {
                    /* @var $action Column\Action\AbstractAction */
                    if ($action->isDisplayed($row) === true) {
                        $action->setTitle($this->translate($action->getTitle()));

                        if ($action->getRoute()) {
                            $action->setLink($this->view->url($action->getRoute(), $action->getRouteParams()));
                        }

                        $actions[] = $action->toHtml($row, $this->translator);
                    }
                }

                $value = implode(' ', $actions);
            }

            // "rowClick" action
            if ($col instanceof Column\Select
                && $rowClickAction instanceof AbstractAction
                && $col->isRowClickEnabled()
            ) {
                $value = '<a href="' . $rowClickAction->getLinkReplaced($row) . '">' . $value . '</a>';
            }

            $attributes = [
                'class'               => implode(' ', $classes),
                'style'               => implode(';', $cssStyles),
                'data-columnUniqueId' => $col->getUniqueId(),
            ];

            $return .= $this->getTd($value, $attributes);
        }

        $return .= $this->getTr($row, false);

        return $return;
    }
}
