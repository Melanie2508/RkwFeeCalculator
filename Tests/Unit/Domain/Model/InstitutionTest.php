<?php
namespace RKW\RkwFeecalculator\Tests\Unit\Domain\Model;

/**
 * Test case.
 *
 * @author Christian Dilger <c.dilger@addorange.de>
 */
class InstitutionTest extends \TYPO3\CMS\Core\Tests\UnitTestCase
{
    /**
     * @var \RKW\RkwFeecalculator\Domain\Model\Institution
     */
    protected $subject = null;

    protected function setUp()
    {
        parent::setUp();
        $this->subject = new \RKW\RkwFeecalculator\Domain\Model\Institution();
    }

    /**
     * @test
     */
    public function getNameReturnsInitialValueForString()
    {
        self::assertSame(
            '',
            $this->subject->getName()
        );

    }

    /**
     * @test
     */
    public function setNameForStringSetsName()
    {
        $this->subject->setName('Conceived at T3CON10');

        self::assertAttributeEquals(
            'Conceived at T3CON10',
            'name',
            $this->subject
        );

    }

    protected function tearDown()
    {
        parent::tearDown();
    }

}
