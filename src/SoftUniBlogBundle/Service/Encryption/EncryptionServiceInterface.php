<?php


namespace SoftUniBlogBundle\Service\Encryption;


interface EncryptionServiceInterface
{
    public function hash(string $password);

    public function verify(string $password, string $hash);
}