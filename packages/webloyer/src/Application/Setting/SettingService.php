<?php

namespace Ngmy\Webloyer\Webloyer\Application\Setting;

use DB;
use Ngmy\Webloyer\Webloyer\Domain\Model\Setting\AppSetting;
use Ngmy\Webloyer\Webloyer\Domain\Model\Setting\AppSettingRepositoryInterface;
use Ngmy\Webloyer\Webloyer\Domain\Model\Setting\DbSetting;
use Ngmy\Webloyer\Webloyer\Domain\Model\Setting\DbSettingDriver;
use Ngmy\Webloyer\Webloyer\Domain\Model\Setting\DbSettingRepositoryInterface;
use Ngmy\Webloyer\Webloyer\Domain\Model\Setting\MailSetting;
use Ngmy\Webloyer\Webloyer\Domain\Model\Setting\MailSettingDriver;
use Ngmy\Webloyer\Webloyer\Domain\Model\Setting\MailSettingRepositoryInterface;
use Ngmy\Webloyer\Webloyer\Domain\Model\Setting\MailSettingSmtpEncryption;

class SettingService
{
    private $appSettingRepository;

    private $dbSettingRepository;

    private $mailSettingRepository;

    /**
     * Create a new application service instance.
     *
     * @param \Ngmy\Webloyer\Webloyer\Domain\Model\Setting\AppSettingRepositoryInterface  $appSettingRepository
     * @param \Ngmy\Webloyer\Webloyer\Domain\Model\Setting\DbSettingRepositoryInterface   $dbSettingRepository
     * @param \Ngmy\Webloyer\Webloyer\Domain\Model\Setting\MailSettingRepositoryInterface $mailSettingRepository
     * @return void
     */
    public function __construct(AppSettingRepositoryInterface $appSettingRepository, DbSettingRepositoryInterface $dbSettingRepository, MailSettingRepositoryInterface $mailSettingRepository)
    {
        $this->appSettingRepository = $appSettingRepository;
        $this->dbSettingRepository = $dbSettingRepository;
        $this->mailSettingRepository = $mailSettingRepository;
    }

    /**
     * Get a app setting.
     *
     * @return \Ngmy\Webloyer\Webloyer\Domain\Model\Setting\AppSetting|null
     */
    public function getAppSetting(): ?AppSetting
    {
        return $this->appSettingRepository->appSetting();
    }

    /**
     * Create or Update app setting.
     *
     * @param string $appSettingUrl
     * @return void
     */
    public function saveAppSetting(string $appSettingUrl): void
    {
        $appSetting = new AppSetting(
            $appSettingUrl
        );
        $this->appSettingRepository->save($appSetting);
    }

    /**
     * Get a db setting.
     *
     * @return \Ngmy\Webloyer\Webloyer\Domain\Model\Setting\DbSetting|null
     */
    public function getDbSetting(): ?DbSetting
    {
        return $this->dbSettingRepository->dbSetting();
    }

    /**
     * Create or Update a db setting.
     *
     * @param string $dbSettingDriver
     * @param string $dbSettingHost
     * @param string $dbSettingDatabase
     * @param string $dbSettingUserName
     * @param string $dbSettingPassword
     * @return void
     */
    public function saveDbSetting(string $dbSettingDriver, string $dbSettingHost, string $dbSettingDatabase, string $dbSettingUserName, string $dbSettingPassword): void
    {
        $dbSetting = new DbSetting(
            new DbSettingDriver($dbSettingDriver),
            $dbSettingHost,
            $dbSettingDatabase,
            $dbSettingUserName,
            $dbSettingPassword
        );
        $this->dbSettingRepository->save($dbSetting);
    }

    /**
     * Get a mail setting.
     *
     * @return \Ngmy\Webloyer\Webloyer\Domain\Model\Setting\MailSetting|null
     */
    public function getMailSetting(): ?MailSetting
    {
        return $this->mailSettingRepository->mailSetting();
    }

    /**
     * Create or Update a mail setting.
     *
     * @param string      $mailSettingDriver
     * @param array       $mailSettingFrom
     * @param string      $mailSettingSmtpHost
     * @param int         $mailSettingSmtpPort
     * @param string|null $mailSettingSmtpEncryption
     * @param string      $mailSettingSmtpUserName
     * @param string      $mailSettingSmtpPassword
     * @param string      $mailSettingSendmailPath
     * @return void
     */
    public function saveMailSetting(string $mailSettingDriver, array $mailSettingFrom, string $mailSettingSmtpHost, int $mailSettingSmtpPort, ?string $mailSettingSmtpEncryption, string $mailSettingSmtpUserName, string $mailSettingSmtpPassword, string $mailSettingSendmailPath): void
    {
        DB::transaction(function () use ($mailSettingDriver, $mailSettingFrom, $mailSettingSmtpHost, $mailSettingSmtpPort, $mailSettingSmtpEncryption, $mailSettingSmtpUserName, $mailSettingSmtpPassword, $mailSettingSendmailPath) {
            if (is_null($mailSettingSmtpEncryption)) {
                $mailSettingSmtpEncryption = null;
            } else {
                $mailSettingSmtpEncryption = new MailSettingSmtpEncryption($mailSettingSmtpEncryption);
            }
            $mailSetting = new MailSetting(
                new MailSettingDriver($mailSettingDriver),
                $mailSettingFrom,
                $mailSettingSmtpHost,
                $mailSettingSmtpPort,
                $mailSettingSmtpUserName,
                $mailSettingSmtpPassword,
                $mailSettingSendmailPath,
                $mailSettingSmtpEncryption
            );
            $this->mailSettingRepository->save($mailSetting);
        });
    }
}
