<?php

namespace packages\domain\model\authTokenManage;

interface IRefreshTokenManageRepository
{
    public function findByToken(RefreshToken $token): RefreshTokenManage;

    public function save(RefreshTokenManage $refreshTokenManage);

    public function delete(RefreshTokenManage $refreshTokenManage);

    public function nextRefreshToken(): RefreshToken;
}