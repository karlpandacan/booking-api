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

use App\Repositories\BookingRepository;

class BookingController extends Controller
{
    protected $bookingRepository;

    public function __construct(BookingRepository $bookingRepository)
    {
        $this->bookingRepository = $bookingRepository;
    }

    public function index(Request $request)
    {
        $params = $request->all();

        $response = new StdClass();
        $response->headers = $this->bookingRepository->listHeader();

        $data = $this->bookingRepository->getData($params);
        $response->data = $data->data;
        $response->pagination = $data->pagination;

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
            $data = $request->all();
            $result = $this->bookingRepository->store($data);
            $this->setStatus($result->success === true ? 200 : 500);
            $this->setSuccess($result->success);
            $this->setMessage($result->message);
            return $this->sendResponse($result->response);
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
            $dateInterval = $request->time_span == '1hour' ? 'PT1H' : 'PT30M';
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
