<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Service\Graph\CalendarBuilder;
use App\Service\Graph\ChartBuilder;
use App\Service\Graph\TreeBuilder;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class StatisticsController
 *
 * @package App\Controller
 */
class StatisticsController extends AbstractController
{
    /**
     * @Route("/statistics", name="app_statistics_index", methods={"GET"})
     * @Route("/user/{username}/statistics", name="app_user_statistics_index", methods={"GET"})
     * @Route("/preview/statistics", name="app_preview_statistics_index", methods={"GET"})
     *
     * @param TreeBuilder $treeBuilder
     * @param CalendarBuilder $calendarBuilder
     * @param ChartBuilder $chartBuilder
     * @return Response
     */
    public function index(TreeBuilder $treeBuilder, CalendarBuilder $calendarBuilder, ChartBuilder $chartBuilder, User $user = null) : Response
    {
        if (!$user instanceof User) {
            $user = $this->getUser();
        }

        $calendar = $calendarBuilder->buildItemCalendar($user);
        ksort($calendar);
        $calendar = array_reverse($calendar, true);

        return $this->render('App/Statistics/index.html.twig', [
            'counters' => $this->getDoctrine()->getRepository(User::class)->getCounters($user),
            'calendarData' => $calendar,
            'treeJson' => json_encode($treeBuilder->buildCollectionTree()),
            'hoursChartData' => $chartBuilder->buildActivityByHour($user),
            'monthsChartData' => $chartBuilder->buildActivityByMonth($user),
            'monthDaysChartData' => $chartBuilder->buildActivityByMonthDay($user),
            'weekDaysChartData' => $chartBuilder->buildActivityByWeekDay($user),
            'itemsEvolutionData' => $chartBuilder->buildItemEvolution($user),
        ]);
    }
}
