import './index.scss';
import DateTime from '@/utils/datetime';
import { PeriodReadableFormat } from '@/utils/period';
import { defineComponent } from '@vue/composition-api';
import Timeline from '@/themes/default/components/Timeline';
//import formatEventAdditionalCostsList from '@/utils/formatEventAdditionalCostsList';

import type Period from '@/utils/period';
import type { PropType } from '@vue/composition-api';
import type { EventDetails, EventAdditionalCost } from '@/stores/api/events';
//import type { AdditionalCostWithDetails } from '@/utils/formatEventAdditionalCostsList';

type Props = {
    /** L'événement dont on souhaite afficher l'onglet des techniciens. */
    event: EventDetails,
};

/** Onglet "AdditionalCosts" de la fenêtre d'un événement. */
const EventDetailsAdditionalCosts = defineComponent({
    name: 'EventDetailsAdditionalCosts',
    props: {
        event: {
            type: Object as PropType<Required<Props>['event']>,
            required: true,
        },
    },
    computed: {

    },
    render() {
        const { events, groups, assignationPeriod } = this;

        return (
            <div class="EventDetailsAdditionalCosts">
                <Timeline
                    class="EventDetailsAdditionalCosts__timeline"
                    period={assignationPeriod}
                    zoomMin={DateTime.duration(1, 'hour')}
                    items={events}
                    groups={groups}
                    hideCurrentTime
                />
            </div>
        );
    },
});

export default EventDetailsAdditionalCosts;
