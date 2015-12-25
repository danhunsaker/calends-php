<?php

use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Behat\Tester\Exception\PendingException;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use Danhunsaker\Calends\Calends;

/**
 * Defines application features from the specific context.
 */
class FeatureContext implements Context, SnippetAcceptingContext
{
    protected $input;

    protected $instance;

    protected $output;

    /**
     * Initializes context.
     *
     * Every scenario gets its own context instance.
     * You can also pass arbitrary arguments to the
     * context constructor through behat.yml.
     */
    public function __construct()
    {
    }

    /**
     * @Transform /^0x([0-9a-fA-F]+)$/
     */
    public function taiHexToIntegers($tai)
    {
        $integers = [];

        switch (strlen($tai)) {
            case 32:
                $integers['atto'] = gmp_strval(gmp_init('0x' . substr($tai, 24), 16), 10);
                $tai              = substr($tai, 0, 24);
            case 24:
                $integers['nano'] = gmp_strval(gmp_init('0x' . substr($tai, 16), 16), 10);
                $tai              = substr($tai, 0, 16);
            case 16:
            default:
                $integers['seconds'] = gmp_strval(gmp_init('0x' . $tai, 16), 10);
                break;
        }

        return array_reverse($integers);
    }

    /**
     * @Transform /^ts:([-_:a-zA-Z0-9]+)$/
     */
    public function namedTimestamp($name)
    {
        switch ($name) {
            case 'max':
                return '4611686018427387903.999999999999999999';
            case 'epoch:unix':
                return '0';
            case 'epoch:jdc':
                return '-210866760000';
            case 'epoch:tai':
            case 'min':
                return '-4611686018427387904';
            case 'now':
            default:
                return microtime(true);
        }
    }

    /**
     * @Given /an input timestamp of ([-0-9a-fA-F.]+|ts:.+)/
     */
    public function anInputTimestampOf($stamp)
    {
        $this->input = $stamp;
    }

    /**
     * @When I create the object using :format
     */
    public function iCreateTheObjectUsing($format)
    {
        $this->instance = new Calends($this->input, $format);
    }

    /**
     * @Then the timestamp used internally should be :stamp
     */
    public function theTimestampUsedInternallyShouldBe($stamp)
    {
        PHPUnit_Framework_Assert::assertEquals($stamp, $this->instance->getInternalTime());
    }

    /**
     * @When I request the :format value
     */
    public function iRequestTheValue($format)
    {
        $this->output = $this->instance->getDate($format);
    }

    /**
     * @Then /the return value should be ([-0-9a-fA-F.]+)/
     */
    public function theReturnValueShouldBe($output)
    {
        PHPUnit_Framework_Assert::assertEquals($output, $this->output);
    }
}
