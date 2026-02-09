<?php

namespace App\Service;

use App\Entity\User;
use App\Repository\ExperienceRepository;

class ExperienceManager
{
    public function __construct(
        private ExperienceRepository $exp_repo,
    ) {}

    public function addExp(User $user, int $exp): array
    {
        $user->setExp($user->getExp() + $exp);
        $new_level = $this->exp_repo->getLevelFromExp($user->getExp());
        $old_level = $user->getLevel();
        $level_up = false;

        if ($new_level > $user->getLevel()) {
            $level_up = true;
            $user->setLevel($new_level);
        }

        return [
            'level_up' => $level_up,
            'new_level' => $new_level,
            'old_level' => $old_level
        ];
    }
}
