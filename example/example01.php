<?php /** @noinspection ALL */

namespace Coff\SMF\Example;

use Coff\SMF\Assertion\AlwaysFalseAssertion;
use Coff\SMF\Exception\ConfigurationException;
use Coff\SMF\Exception\SchemaException;
use Coff\SMF\Exception\TransitionException;
use Coff\SMF\Machine;
use Coff\SMF\Schema\Schema;
use Coff\SMF\StateEnum;
use Coff\SMF\Transition\Transition;

include(__DIR__ . '/../vendor/autoload.php');

/**
 * @method static DRAFT()
 * @method static SENT()
 * @method static VOTED()
 * @method static ACCEPTED()
 * @method static REJECTED()
 * @method static CANCELED()
 */
class PetitionEnum extends StateEnum
{
    const __default = self::DRAFT,
        DRAFT = 'draft',
        SENT = 'sent',
        VOTED = 'voted',
        ACCEPTED = 'accepted',
        REJECTED = 'rejected',
        CANCELED = 'canceled';

}

class Petition extends Machine
{

    protected $votesYes, $votesNo;

    /**
     * @throws TransitionException
     * @throws ConfigurationException
     * @throws SchemaException
     */
    public function send()
    {
        // shall throw an exception if current state is not DRAFT because it wasn't allowed transition
        $this->setMachineState(PetitionEnum::SENT());
    }

    /**
     * @throws ConfigurationException
     * @throws SchemaException
     * @throws TransitionException
     */
    public function cancel()
    {
        // shall throw an exception if current state is not DRAFT because it wasn't allowed transition
        $this->setMachineState(PetitionEnum::CANCELED());
    }


    public function setVotes($ya, $nay)
    {
        $this->votesYes = $ya;
        $this->votesNo = $nay;
    }

    public function assertSentToVoted()
    {
        // Method name used here is based upon DefaultCallbackAssertion. This can be changed though.
        return (null !== $this->votesYes && null !== $this->votesNo) ? true : false;
    }


    public function assertVotedToAccepted()
    {
        return $this->votesYes > $this->votesNo ? true : false;
    }

    public function assertVotedToRejected()
    {
        return $this->votesYes <= $this->votesNo ? true : false;
    }

    public function onTransition(Transition $transition)
    {
        /* for purpose of this example we only echo this transition but you can
           easily dispatch an event from here */
        echo 'State changed from ' . $transition->getFromState() . ' to ' . $transition->getToState() . PHP_EOL;
    }
}

$Schema = new Schema();

$schema
    ->setInitState(PetitionEnum::DRAFT())
    // prevents changing state upon assertion when AlwaysFalseAssertion is given
    ->allowTransition(PetitionEnum::DRAFT(), PetitionEnum::SENT(), new AlwaysFalseAssertion())
    ->allowTransition(PetitionEnum::DRAFT(), PetitionEnum::CANCELED(), new AlwaysFalseAssertion())
    // when no Assertion is given uses DefaultCallbackAssertion which calls assertXToY methods
    ->allowTransition(PetitionEnum::SENT(), PetitionEnum::VOTED())
    ->allowTransition(PetitionEnum::VOTED(), PetitionEnum::ACCEPTED())
    ->allowTransition(PetitionEnum::VOTED(), PetitionEnum::REJECTED());

$p = new Petition();
$p
    ->setSchema($schema)
    ->init();

echo 'Running...' . PHP_EOL;
$p->run();

echo 'Sending...' . PHP_EOL;
$p->send();

echo 'Running...' . PHP_EOL;
$p->run();

echo 'Setting votes...' . PHP_EOL;
$p->setVotes(5, 1);

echo 'Running...' . PHP_EOL;
$p->run();
