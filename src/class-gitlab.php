<?php
namespace RUpdater;

class Gitlab {
    const RESOURCES = [
        'last_tag' => 'https://gitlab.com/api/v4/projects/%s/repository/tags',
    ];

    private $credentials;
    private $repository;
    private $authentication_type;

    public function __construct(string $repository, string $authentication_type, array $credentials) {
        $this->repository = $repository;
        $this->credentials = $credentials;
        $this->authentication_type = $authentication_type;
    }

    private function get_new_realease() {}
}