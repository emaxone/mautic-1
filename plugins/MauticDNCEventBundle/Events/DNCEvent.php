<?php

namespace MauticPlugin\MauticDNCEventBundle\Events;

use Mautic\CampaignBundle\CampaignEvents;
use Mautic\CampaignBundle\Event\CampaignBuilderEvent;
use Mautic\CampaignBundle\Event\CampaignExecutionEvent;
use Mautic\CoreBundle\EventListener\CommonSubscriber;
use Mautic\CoreBundle\Factory\MauticFactory;
use MauticPlugin\MauticDNCEventBundle\MauticDNCEventEvents;

class DNCEvent extends CommonSubscriber
{
    protected $factory;

    /**
     * CampaignSubscriber constructor.
     *
     * @param MauticFactory $factory
     */
    public function __construct(MauticFactory $factory)
    {
        $this->factory = $factory;
    }

    /** Reescreve o metodo da classe CommonSubscriber
     * Retorna a lista de eventos que esta classe vai registrar.
     *
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            CampaignEvents::CAMPAIGN_ON_BUILD         => ['onCampaignBuild', 0],
            MauticDNCEventEvents::DNCEVENT_ADD_DNC    => ['executeCampaignActionAddDNC', 0],
        ];
    }

    /**
     * @param CampaignBuilderEvent $event
     */
    public function onCampaignBuild(CampaignBuilderEvent $event)
    {
        // Register custom action
        $event->addAction(
            'dncevent.addDnc',
            [
                'label'       => 'plugin.dncevent.campaign.addDnc.label',
                'eventName'   => MauticDNCEventEvents::DNCEVENT_ADD_DNC,
                'description' => 'plugin.dncevent.campaign.addDnc.desc',
                'formType'    => 'dncevent_add_type_form',
            ]
        );
    }

    /**
     * Execute campaign action.
     *
     * @param CampaignExecutionEvent $event
     */
    public function executeCampaignActionAddDNC(CampaignExecutionEvent $event)
    {
        $lead = $event->getLead();

        $config = $event->getConfig();

        $model = $this->factory->getModel('dncevent.model');
        $res   = $model->addDoNotContact($lead, $config['properties']);

        $event->setResult($res);
    }
}
