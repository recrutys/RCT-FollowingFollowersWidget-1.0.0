<?php

namespace RCT\FollowingFollowersWidget\Widget;

use XF;
use XF\Http\Request;
use XF\Widget\AbstractWidget;
use XF\Widget\WidgetRenderer;

class Followers extends AbstractWidget
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


        $userFollowersFinder = $userFollowRepo->findFollowersForProfile($user);
        $userFollowersFinder->order($userFollowersFinder->expression('RAND()'));

        $followers = $userFollowersFinder->fetch($this->options['limit'])->pluckNamed('User');
        $followersCount = $userFollowersFinder->total();

        if (!$followers->count())
        {
            return '';
        }

        $viewParams = [
            'user' => $user,
            'followers' => $followers,
            'followersCount' => $followersCount,
        ];

        return $this->renderer('rct_followers_widget', $viewParams);
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