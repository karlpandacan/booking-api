<?php

namespace App\Repositories;

use Log;
use StdClass;
use Exception;
use DateTime;
use DateInterval;

use App\Models\Booking;
use App\Repositories\Interfaces\BookingRepositoryInterface;
use Throwable;

class BookingRepository extends Repository implements BookingRepositoryInterface
{

    protected $booking;

    public function __construct(Booking $booking)
    {
        parent::__construct($booking);
    }

    /** 
     * @param (boolean)$hasActions 
     * @return (array) of (objects)
     */
    public function listHeader($hasActions = true): array
    {
        $response =  [
            (object) ['text' => 'User', 'align' => 'center', 'value' => 'fullname'],
            (object) ['text' => 'Date', 'align' => 'center', 'value' => 'date'],
            (object) ['text' => 'Room', 'align' => 'center', 'value' => 'room'],
            (object) ['text' => 'Time From', 'align' => 'center', 'value' => 'time_from'],
            (object) ['text' => 'Time To', 'align' => 'center', 'value' => 'time_to'],
        ];

        if ($hasActions) {
            $response[] = (object) ['text' => 'Actions', 'align' => 'center', 'sortable' => false, 'value' => 'actions'];
        }
        return $response;
    }

    /**
     * @param (array)$data
     * @return (object) { success, data, pagination }
     */
    public function getData($data): object
    {
        try {
            $params = new StdClass();
            $params->dateFrom = $data->date_from ?? date('Y-m-d');
            $params->dateTo = $data->date_to ?? date('Y-m-d');
            $search = $data->search ?? "";
            $myBookings = $data->mybooking ?? 0;
            $bookings = $this->model->filterDate($params)->search($search)
                ->with(['room', 'user']);
            if ($myBookings == 1) {
                $bookings = $bookings->where('user_id', $data->userId);
            }
            if ($data->has('sort')) {
                $sort = $data->sort;
                $sord = $data->sord;
                switch ($sort) {
                    case 'fullname':
                        $bookings = $bookings->with(['user' => fn ($query) => $query->orderBy('fullname', $sord)]);
                        break;
                    case 'room':
                        $bookings = $bookings->with(['room' => fn ($query) => $query->orderBy('name', $sord)]);
                        break;
                    case 'date':
                    case 'time_from':
                        $bookings = $bookings->orderBy('date_from', $data->sord);
                        break;
                    case 'time_to':
                        $bookings = $bookings->orderBy('date_to', $data->sord);
                        break;
                    default:
                        $bookings = $bookings->orderBy($data->sort, $data->sord);
                        break;
                }
            }

            $bookings = $bookings->paginate(Booking::PER_PAGES);

            $response = new StdClass();
            $response->success = [];
            $response->data = [];
            $items = $bookings->items();
            if (!empty($items)) {
                foreach ($items as $item) {
                    $response->data[] = (object)[
                        'id' => $item->id,
                        'fullname' => $item->user->fullname,
                        'date' => $item->date_from->format('Y-m-d'),
                        'room' => $item->room->name,
                        'time_from' => $item->date_from->format('H:i:s'),
                        'time_to' => $item->date_to->format('H:i:s'),
                    ];
                }
            }

            $response->pagination = new StdClass();
            $response->pagination->total = $bookings->total();
            $response->pagination->first_item = $bookings->firstItem();
            $response->pagination->last_item = $bookings->lastItem();
            $response->pagination->current_page = $bookings->currentPage();
            $response->pagination->last_page = $bookings->lastPage();
        } catch (Throwable $exception) {
            $response->success = false;
            $response->data = [];
            $response->pagination = new StdClass;
        }

        return $response;
    }

    /**
     * @param (array)$data
     * @return (object) { success, message, booking }
     */
    public function store($data): object
    {
        $response = new StdClass();
        try {

            $dateFrom = $data->date . " " . $data->time;
            $booking = $this->model->create([
                'user_id' => $data->userId,
                'room_id' => $data->room_id,
                'date_from' => $dateFrom,
                'date_to' => $this->getTimeInterval($dateFrom, $data->time_span),
            ]);
            $response->success = true;
            $response->message = 'Success booking a room.';
            $response->response = $booking;
            return $response;
        } catch (Throwable $exception) {
            $response->success = false;
            $response->message = 'Something went wrong! Please contact the administrator.';
            $response->response = $exception->getMessage();
            return $response;
        }
    }

    /**
     * @param (array)$dateFrom, 
     * @return (object) { data, pagination }
     */
    private function getTimeInterval($dateFrom, $timeSpan): string
    {
        $newDateTime = new DateTime($dateFrom);
        $dateInterval = $timeSpan == '1hour' ? 'PT1H' : 'PT30M';
        return $newDateTime->add(new DateInterval($dateInterval))->format('Y-m-d H:i:s');
    }
}
