import { z } from '@/utils/validation';
import requester from '@/globals/requester';

import { EventSchema } from './events';
import { withPaginationEnvelope } from './@schema';

import type Period from '@/utils/period';
import type { Event } from './events';
import type { SchemaInfer } from '@/utils/validation';
import type { AxiosRequestConfig as RequestConfig } from 'axios';
import type { PaginatedData, SortableParams, PaginationParams } from './@types';
import type Decimal from 'decimal.js';


// ------------------------------------------------------
// -
// -    Schema / Enums
// -
// ------------------------------------------------------

//
// - Schemas secondaires
//

export const AdditionalCostEventSchema = z.strictObject({
    id: z.number(),
    event_id: z.number(),
    event: z.lazy(() => EventSchema),
});

//
// - Schemas principaux
//

export const AdditionalCostSchema = z.strictObject({
    id: z.number(),
    cost_price: z.decimal(),
    name: z.string(),
    description: z.string().nullable(),
    created_at: z.datetime(),
});

export const AdditionalCostWithEventsSchema = AdditionalCostSchema.extend({
    events: AdditionalCostEventSchema.array(),
});

// ------------------------------------------------------
// -
// -    Types
// -
// ------------------------------------------------------

export type AdditionalCost = SchemaInfer<typeof AdditionalCostSchema>;

export type AdditionalCostEvent = SchemaInfer<typeof AdditionalCostEventSchema>;
export type AdditionalCostWithEvents = SchemaInfer<typeof AdditionalCostWithEventsSchema>;

//
// - Edition
//

export type AdditionalCostEdit = {
    name: string,
    description: string,
    cost_price: string,
    //and more...
};

//
// - Récupération
//

export type Filters = {
    search?: string,
};

type GetAllParams = (
    & Filters
    & SortableParams
    & PaginationParams
    & { deleted?: boolean }
);

// ------------------------------------------------------
// -
// -    Fonctions
// -
// ------------------------------------------------------
const all = async (params: GetAllParams = {}): Promise<PaginatedData<AdditionalCost[]>> => {
    const response = await requester.get('/additional-costs', { params });
    return withPaginationEnvelope(AdditionalCostSchema).parse(response.data);
};

const allWhileEvent = async (eventId: Event['id']): Promise<AdditionalCostWithEvents[]> => {
    const response = await requester.get(`/additional-costs/while-event/${eventId}`);
    return AdditionalCostWithEventsSchema.array().parse(response.data);
};

const one = async (id: AdditionalCost['id']): Promise<AdditionalCost> => {
    const response = await requester.get(`/additional-costs/${id}`);
    return AdditionalCostSchema.parse(response.data);
};

const create = async (data: AdditionalCostEdit): Promise<AdditionalCost> => {
    const response = await requester.post('/additional-costs', data);
    return AdditionalCostSchema.parse(response.data);
};

const update = async (id: AdditionalCost['id'], data: AdditionalCostEdit): Promise<AdditionalCost> => {
    const response = await requester.put(`/additional-costs/${id}`, data);
    return AdditionalCostSchema.parse(response.data);
};

const remove = async (id: AdditionalCost['id']): Promise<void> => {
    await requester.delete(`/additional-costs/${id}`);
};



export default {
    all,
    allWhileEvent,
    one,
    create,
    update,
    remove,
};
