import config from '@/globals/config';
import requester from '@/globals/requester';

import type { Person, PersonEdit } from './persons';

//
// - Types
//

export type Technician = Omit<Person, 'reference' | 'company' | 'company_id'>;
export type TechnicianEdit = Omit<PersonEdit, 'reference' | 'company_id'>;

//
// - Fonctions
//

const one = async (id: Technician['id']): Promise<Technician> => (
    (await requester.get(`/persons/${id}`)).data
);

const create = async (data: TechnicianEdit): Promise<Technician> => {
    const _data = { ...data, tags: [config.technicianTagName] };
    return (await requester.post('/persons', _data)).data;
};

const update = async (id: Technician['id'], data: TechnicianEdit): Promise<Technician> => (
    (await requester.put(`/persons/${id}`, data)).data
);

const remove = async (id: Technician['id']): Promise<void> => {
    await requester.delete(`/persons/${id}`);
};

export default { one, create, update, remove };
