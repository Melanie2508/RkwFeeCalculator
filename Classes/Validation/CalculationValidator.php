<?php

namespace Rkw\RkwFeecalculator\Validation;

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

use RKW\RkwBasics\Helper\Common;

/**
 * Class CalculationValidator
 *
 * @author Christian Dilger <c.dilger@addorange.de>
 * @copyright Rkw Kompetenzzentrum
 * @package RKW_RkwFeeCalculator
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class CalculationValidator extends \TYPO3\CMS\Extbase\Validation\Validator\AbstractValidator
{

    /**
     * TypoScript Settings
     *
     * @var array
     */
    protected $settings = null;

    /**
     * validation
     *
     * @var \Rkw\RkwFeecalculator\Domain\Model\Calculation $objectSource
     * @return boolean
     * @throws \TYPO3\CMS\Extbase\Configuration\Exception\InvalidConfigurationTypeException
     */
    public function isValid($objectSource)
    {

        // initialize typoscript settings
        $this->getSettings();

        // get mandatory fields
        $mandatoryFields = \TYPO3\CMS\Core\Utility\GeneralUtility::trimExplode(',', $this->settings['mandatoryFields']['calculation']);
        $mandatoryFields = ['selectedProgram', 'days', 'consultantFeePerDay'];

        $isValid = true;

        if (! $objectSource->getInitialized()) {
            $objectSource->setInitialized(true);
            $mandatoryFields = ['selectedProgram'];
        }

        /*
        $possibleDaysMin = $objectSource->getSelectedProgram()->getPossibleDaysMin();
        $possibleDaysMax = $objectSource->getSelectedProgram()->getPossibleDaysMax();

        if (
            $possibleDaysMin > 0
            && $possibleDaysMax > 0
        ){

            if ($possibleDaysMin > $objectSource->getDays()) {
                $isValid = false;
            }

            if ($possibleDaysMax < $objectSource->getDays()) {
                $isValid = false;
            }

        }
        */

        //  properties
        $requiredGetters = array_filter(get_class_methods($objectSource), function ($method) use ($mandatoryFields) {
            return strpos($method, 'get') !== false && in_array(lcfirst(substr($method, 3)), $mandatoryFields);
        });

        foreach ($requiredGetters as $getter) {

            $property = lcfirst(substr($getter, 3));
            $field = \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate(
                'tx_rkwfeecalculator_domain_model_calculation.form.' . \TYPO3\CMS\Core\Utility\GeneralUtility::camelCaseToLowerCaseUnderscored($property),
                'rkw_feecalculator'
            );

            if ($getter === 'getConsultantFeePerDay') {
                $getter = 'getRawConsultantFeePerDay';
            }

            if (!trim($objectSource->$getter())) {
                $this->result->forProperty($property)->addError(
                    new \TYPO3\CMS\Extbase\Error\Error(
                        \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate(
                            'validator.notempty.empty',
                            'rkw_feecalculator',
                            [$field]
                        ), 1449314603
                    )
                );
                $isValid = false;
            }

            if ($getter === 'getRawConsultantFeePerDay') {
                if (! preg_match('/^(\d+(?:[\.\,]\d{2})?)$/', $objectSource->$getter())) {
                    $this->result->forProperty(lcfirst(substr($getter, 3)))->addError(
                        new \TYPO3\CMS\Extbase\Error\Error(
                            \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate(
                                'validator.number.notvalid',
                                'rkw_feecalculator',
                                [$field]
                            ), 1449314604
                        )
                    );
                    $isValid = false;
                }
            }

        }

        return $isValid;
        //===
    }

    /**
     * Returns TYPO3 settings
     *
     * @return array
     * @throws \TYPO3\CMS\Extbase\Configuration\Exception\InvalidConfigurationTypeException
     */
    protected function getSettings()
    {

        $this->settings = null;

        if (!$this->settings) {
            $this->settings = Common::getTyposcriptConfiguration('RkwFeecalculator');
        }

        if (!$this->settings) {
            return array();
        }

        return $this->settings;
        //===
    }

}

