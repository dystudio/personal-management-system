<?php

namespace App\Services\Settings;

use App\Controller\Page\SettingsController;
use App\Controller\Page\SettingsDashboardController;
use App\DTO\Settings\Dashboard\Widget\SettingsWidgetVisibilityDTO;
use App\DTO\Settings\SettingsDashboardDTO;
use App\Entity\Setting;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Class SettingsSaver
 * @package App\Services\Files
 */
class SettingsSaver {

    /**
     * @var EntityManagerInterface $em
     */
    private $em;

    /**
     * @var SettingsLoader $settings_loader
     */
    private $settings_loader;

    /**
     * @param EntityManagerInterface $em
     * @param SettingsLoader $settings_loader
     */
    public function __construct(
        EntityManagerInterface  $em,
        SettingsLoader          $settings_loader

    ) {
        $this->em = $em;
        $this->settings_loader = $settings_loader;
    }

    public function saveSettingsForDashboardFromDto(SettingsDashboardDTO $dto){
        $json = $dto->toJson();

        $setting = new Setting();
        $setting->setName(SettingsLoader::SETTING_NAME_DASHBOARD);
        $setting->setValue($json);

        $this->em->persist($setting);
        $this->em->flush();
    }

    /**
     * @param SettingsWidgetVisibilityDTO[] $array_of_widgets_visibility_dto
     * @throws \Exception
     */
    public function saveSettingsForDashboardWidgetsVisibility(array $array_of_widgets_visibility_dto): void {

        $setting = $this->settings_loader->getSettingsForDashboard();

        $are_settings_in_db = !empty($setting);

        if( $are_settings_in_db ){
            $setting_json = $setting->getValue();
            $dto          = SettingsDashboardDTO::fromJson($setting_json);

            $dto->getWidgetSettings()->setWidgetVisibility($array_of_widgets_visibility_dto);
        }else{
            $setting = new Setting();
            $dto     = SettingsDashboardController::buildDashboardSettingsDto($array_of_widgets_visibility_dto);
        }

        $dashboard_settings_json = $dto->toJson();

        $setting->setName(SettingsController::KEY_DASHBOARD_SETTINGS);
        $setting->setValue($dashboard_settings_json);

        $this->em->persist($setting);
        $this->em->flush();
    }

}