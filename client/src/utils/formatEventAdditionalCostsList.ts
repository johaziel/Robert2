import type { EventAdditionalCost } from '@/stores/api/events';
import type { AdditionalCost } from '@/stores/api/additional_costs';


export type AdditionalCostWithDetails = {
    id: AdditionalCost['id'],
    name: AdditionalCost['name'],
    description: AdditionalCost['description'],
    cost_price: AdditionalCost['cost_price'],
};

const formatEventAdditionalCostsList = (eventAdditionalCosts: EventAdditionalCost[] | null | undefined): AdditionalCostWithDetails[] => {
    if (!Array.isArray(eventAdditionalCosts) || eventAdditionalCosts.length === 0) {
        return [];
    }

    const additionalCosts = new Map<EventAdditionalCost['id'], AdditionalCostWithDetails>();
    eventAdditionalCosts.forEach(({  id, name, description,cost_price}: EventAdditionalCost) => {

        //const { id_ad, name_ad, description_ad } = additionalCost;

        //if (!additionalCosts.has(id)) {
        //    additionalCosts.set(id, { id, name, description  });
        //}

    });

    return Array.from(additionalCosts.values());
};

export default formatEventAdditionalCostsList;
