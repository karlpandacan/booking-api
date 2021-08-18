<?php

/**
 * @author: Karl Pandacan
 * @page: Booking Controller
 * @created: 2021-08-18
 */

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use StdClass;
use Throwable;

use App\Models\Room;
use App\Models\Booking;
use DateInterval;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        $params = new StdClass();
        $params->dateFrom = $request->date_from ?? date('Y-m-d');
        $params->dateTo = $request->date_to ?? date('Y-m-d');
        $search = $request->search ?? "";
        $myBookings = $request->mybooking ?? 0;
        $bookings = Booking::filterDate($params)->search($search)
            ->with(['room', 'user']);
        if ($myBookings == 1) {
            $bookings = $bookings->where('user_id', $request->userId);
        }
        if ($request->has('sort')) {
            $sort = $request->sort;
            $sord = $request->sord;
            switch ($sort) {
                case 'fullname':
                    $bookings = $bookings->with(['user' => fn ($query) => $query->orderBy('fullname', $sord)]);
                    break;
                case 'room':
                    $bookings = $bookings->with(['room' => fn ($query) => $query->orderBy('name', $sord)]);
                    break;
                case 'date':
                case 'time_from':
                    $bookings = $bookings->orderBy('date_from', $request->sord);
                    break;
                case 'time_to':
                    $bookings = $bookings->orderBy('date_to', $request->sord);
                    break;
                default:
                    $bookings = $bookings->orderBy($request->sort, $request->sord);
                    break;
            }
        }

        $bookings = $bookings->paginate(Booking::PER_PAGES);

        $response = new StdClass();
        $response->headers = [
            (object) ['text' => 'User', 'align' => 'center', 'value' => 'fullname'],
            (object) ['text' => 'Date', 'align' => 'center', 'value' => 'date'],
            (object) ['text' => 'Room', 'align' => 'center', 'value' => 'room'],
            (object) ['text' => 'Time From', 'align' => 'center', 'value' => 'time_from'],
            (object) ['text' => 'Time To', 'align' => 'center', 'value' => 'time_to'],
        ];
        if ($request->userId != "") {
            $response->headers[] = (object) ['text' => 'Actions', 'align' => 'center', 'sortable' => false, 'value' => 'actions'];
        }

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

        $this->setMessage('Success Getting Bookings');
        return $this->sendResponse($response);
    }


    public function show(Booking $booking)
    {
        try {
            $this->setMessage('Success fetching booking.');
            return $this->sendResponse($booking);
        } catch (Throwable $exception) {
            $this->setStatus(500);
            $this->setSuccess(false);
            $this->setMessage('Something went wrong. Please contact the admin.');
            return $this->sendResponse([$exception->getMessage()]);
        }
    }

    public function store(Request $request)
    {
        try {
            $dateFrom = $request->date . " " . $request->time;
            $newDateTime = new \DateTime($dateFrom);
            $dateInterval = $request->time_stap == '1hour' ? 'PT1H' : 'PT30M';
            $dateTo = $newDateTime->add(new DateInterval($dateInterval))->format('Y-m-d H:i:s');
            $booking = new Booking();
            $booking->user_id = $request->userId;
            $booking->room_id = $request->room;
            $booking->date_from = $dateFrom;
            $booking->date_to = $dateTo;
            $booking->save();

            $this->setMessage('Success booking a room.');
            return $this->sendResponse([]);
        } catch (Throwable $exception) {
            $this->setStatus(500);
            $this->setSuccess(false);
            $this->setMessage('Something went wrong. Please contact the admin.');
            return $this->sendResponse([$exception->getMessage()]);
        }
    }

    public function update(Request $request, Booking $booking)
    {
        try {
            $dateFrom = $request->date . " " . $request->time;
            $newDateTime = new \DateTime($dateFrom);
            $dateInterval = $request->time_stap == '1hour' ? 'PT1H' : 'PT30M';
            $dateTo = $newDateTime->add(new DateInterval($dateInterval))->format('Y-m-d H:i:s');
            $booking->room_id = $request->room;
            $booking->date_from = $dateFrom;
            $booking->date_to = $dateTo;
            $booking->save();

            $this->setMessage('Success updating booking.');
            return $this->sendResponse([]);
        } catch (Throwable $exception) {
            $this->setStatus(500);
            $this->setSuccess(false);
            $this->setMessage('Something went wrong. Please contact the admin.');
            return $this->sendResponse([$exception->getMessage()]);
        }
    }

    public function destroy(Booking $booking)
    {
        try {
            $booking->delete();
            $this->setMessage('Booking deleted.');
            return $this->sendResponse([]);
        } catch (Throwable $exception) {
            $this->setStatus(500);
            $this->setSuccess(false);
            $this->setMessage('Something went wrong. Please contact the admin.');
            return $this->sendResponse([$exception->getMessage()]);
        }
    }

    public function availableRoom(Request $request)
    {

        try {
            $params = new StdClass();
            $params->dateFrom = $request->date . " " . $request->time;
            $newDateTime = new \DateTime($params->dateFrom);
            $dateInterval = $request->interval == '1hour' ? 'PT1H' : 'PT30M';
            $params->dateTo = $newDateTime->add(new DateInterval($dateInterval))
                ->format('Y-m-d H:i:s');
            $bookings = Booking::filterDateTime($params);
            if ($request->has('booking') && $request->booking != "") {
                $bookings = $bookings->where('id', '!=', $request->booking);
            }
            $bookings = $bookings->groupBy('room_id')
                ->pluck('room_id')
                ->toArray();

            $rooms = Room::whereNotIn('id', $bookings)->get(['id', 'name']);

            $this->setMessage('Success getting available rooms.');
            return $this->sendResponse($rooms);
        } catch (Throwable $exception) {
            $this->setStatus(500);
            $this->setSuccess(false);
            $this->setMessage('Something went wrong. Please contact the admin.');
            return $this->sendResponse([$exception->getMessage()]);
        }
    }
}
