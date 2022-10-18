<?php

namespace RCT\FollowingFollowersWidget\Widget;

use XF;
use XF\Http\Request;
use XF\Widget\AbstractWidget;
use XF\Widget\WidgetRenderer;

class Following extends AbstractWidget
{
    protected $defaultOptions = [
        'limit' => 12
    ];

    public function render()
    {
        if (!isset($this->contextParams['user']))
        {
            return '';
        }

        $user = $this->contextParams['user'];

        /** @var \XF\Repository\UserFollow $userFollowRepo */
        $userFollowRepo = $this->repository('XF:UserFollow');

        $following = [];
        $followingCount = 0;
        if ($user->Profile->following)
        {
            $userFollowingFinder = $userFollowRepo->findFollowingForProfile($user);
            $userFollowingFinder->order($userFollowingFinder->expression('RAND()'));

            $following = $userFollowingFinder->fetch($this->options['limit'])->pluckNamed('FollowUser');
            $followingCount = $userFollowingFinder->total();
        }

        if (empty($following))
        {
            return '';
        }

        $viewParams = [
            'user' => $user,
            'following' => $following,
            'followingCount' => $followingCount,
        ];

        return $this->renderer('rct_following_widget', $viewParams);
    }

    public function verifyOptions(Request $request, array &$options, &$error = null): bool
    {
        $options = $request->filter([
            'limit' => 'uint'
        ]);

        if ($options['limit'] < 1)
        {
            $options['limit'] = 1;
        }

        return true;
    }

}