<?php


namespace App\Service;


use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;

/**
 * Class TokenService
 * @package App\Service
 */
class TokenService
{
    /**
     * @var TokenGeneratorInterface
     */
    private $tokenGenerator;

    /**
     * @var string phrase de cryptage
     */
    private $passPhrase;

    /**
     * @var int temps en seconde
     */
    private $lifeTime;

    /**
     * delimiter du lien hash
     */
    const DELIMITER_HASHLINK = '%';

    /**
     * TokenService constructor.
     * @param string $passPhrase
     * @param string $lifeTime
     * @param TokenGeneratorInterface $tokenGenerator
     */
    public function __construct(string $passPhrase, string $lifeTime, TokenGeneratorInterface $tokenGenerator)
    {
        $this->tokenGenerator = $tokenGenerator;
        $this->passPhrase = $passPhrase;
        $this->lifeTime = intval($lifeTime);
    }

    /**
     * @param string $publicId
     * @return string
     * @throws \Exception
     * Genere un token hash
     */
    public function generate(string $publicId):string
    {
        $date = new \DateTime('now');
        $timestamp = $date->getTimestamp();
        $token = $this->tokenGenerator->generateToken();
        $encode = $timestamp . self::DELIMITER_HASHLINK . $token .
            self::DELIMITER_HASHLINK . $publicId . self::DELIMITER_HASHLINK . $this->passPhrase;
        $hash = hash('sha1', $encode) . self::DELIMITER_HASHLINK . $timestamp . self::DELIMITER_HASHLINK . $token;
        return $hash;
    }

    /**
     * @param string $token
     * @return array
     * retourne le token hash généré sous form de tableaux pour récupérer ses clefs
     */
    private function explode(string $token): array
    {
        $key = explode(self::DELIMITER_HASHLINK, $token);
        return $key;
    }

    /**
     * @param string $token
     * @param string $publicId
     * @return string string
     * récupére le hash
     */
    private function getHash(string $token, string $publicId): string
    {
        $key = $this->explode($token);
        $keyPass = hash('sha1', $key[1] . self::DELIMITER_HASHLINK . $key[2] . self::DELIMITER_HASHLINK . $publicId
            . self::DELIMITER_HASHLINK . $this->passPhrase);
        return $keyPass;
    }

    /**
     * @param string $token
     * @return int l'interval de temps entre la génération du hash et le présent
     * @throws \Exception
     */
    private function getRemainingTime(string $token): int
    {
        $key = explode(self::DELIMITER_HASHLINK, $token);
        $time = new \DateTime();
        $time = $time->getTimestamp();
        $interval = $time - intval($key[1]);
        return $interval;
    }

    /**
     * @param string $token
     * @param string $publicId
     * @return bool
     * @throws \Exception
     * vérifie la correspondence du hash et le temps de validation
     */
    public function isValid(string $token, string $publicId): bool
    {
        $validToken = true;
        $key = $this->explode($token);
        $keyPass = $this->getHash($token, $publicId);
        $interval = $this->getRemainingTime($token);
        if ($interval > $this->lifeTime || $keyPass != $key[0]) {
            $validToken = false;
        };
        return $validToken;
    }
}
