<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Ticket;
use App\Models\User;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreTicketRequest;
use App\Http\Requests\Api\V1\UpdateTicketRequest;
use App\Http\Requests\Api\V1\ReplaceTicketRequest;
use App\Http\Resources\V1\TicketResource;
use App\Http\Filters\V1\TicketFilter;
use App\Policies\V1\TicketPolicy;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class TicketController extends ApiController
{
    protected $policyClass = TicketPolicy::class;
    /**
     * Get all tickets
     *
     * @group Managing Tickets
     * @queryParam sort string Data field(s) to sort by. Seperate multiple fields with commas. Denote descending sort with a minus sign. Example: sort=title,-createdAt
     * @queryParam filter[status] Filter by status code: A, C, H, X. No-example
     * @queryParam filter[title] Filter by title. Wildcards are supported. Example: *fix*
     */
    public function index(TicketFilter $filters)
    {
        return TicketResource::collection(Ticket::filter($filters)->paginate());
    }

    /**
     * Create a ticket
     *
     * Creates a new ticket. Users can only create tickets for themselves. Managers can create tickets for any user.
     *
     * @group Managing Tickets
     */
    public function store(StoreTicketRequest $request)
    {
            // $user = User::findOrFail($request->input('data.relationships.author.data.id'));

            if($this->isAble('store', Ticket::class)) {
                return new TicketResource(Ticket::create($request->mappedAttributes()));
            }

            return $this->notAuthorized('You are not authorized to update that resource', 401);

        // $model = [
        //     'title' => $request->input('data.attributes.title'),
        //     'description' => $request->input('data.attributes.description'),
        //     'status' => $request->input('data.attributes.status'),
        //     'user_id' => $request->input('data.relationships.author.data.id')
        // ];

        // return new TicketResource(Ticket::create($model));
    }

    /**
     * Display the specified resource.
     */
    public function show(Ticket $ticket)
    {
            if ($this->include('author')) {
                return  new TicketResource($ticket->load('user'));
            }

            return new TicketResource($ticket);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTicketRequest $request, $ticket_id)
    {

            if ($this->isAble('update', $ticket)) {
                $ticket->update($request->mappedAttributes());

                return new TicketResource($ticket);
            }
            return $this->notAuthorized('You are not authorized to update that resource', 401);
    }

    public function replace(ReplaceTicketRequest $request, $ticket)
    {
            // $ticket = Ticket::findOrFail($ticket_id);

            if ($this->isAble('replace', $ticket)) {
                $ticket->update($request->mappedAttributes());

                return new TicketResource($ticket);
            }

            return $this->notAuthorized('You are not authorized to update that resource', 401);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($ticket)
    {
            // $ticket = Ticket::findOrFail($ticket_id);

            if ($this->isAble('delete', $ticket)) {
                $ticket->delete();

                return $this->ok('Ticket successfully deleted');
            }

            return $this->notAuthorized('You are not authorized to update that resource', 401);
    }
}
