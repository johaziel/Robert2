import requester from '@/globals/requester';
import apiBookings from '@/stores/api/bookings';
import Period from '@/utils/period';
import { withPaginationEnvelope } from '@fixtures/@utils';
import data from '@fixtures/bookings';

describe('Bookings Api', () => {
    describe('all()', () => {
        it('parse the returned data correctly when paginated', async () => {
            const paginatedData = withPaginationEnvelope(data.excerpt());

            jest.spyOn(requester, 'get').mockResolvedValue({ data: paginatedData });
            expect(await apiBookings.all()).toMatchSnapshot();
        });

        it('parse the returned data correctly when not paginated', async () => {
            jest.spyOn(requester, 'get').mockResolvedValue({ data: data.excerpt() });
            const period = new Period('2024-01-01', '2024-01-02', true);
            const response = await apiBookings.all({ paginated: false, period });
            expect(response).toMatchSnapshot();
        });
    });

    describe('oneSummary()', () => {
        it.each(data.summary())('parse the returned data correctly', async (datum: any) => {
            jest.spyOn(requester, 'get').mockResolvedValue({ data: datum });
            expect(await apiBookings.oneSummary(datum.entity, datum.id)).toMatchSnapshot();
        });
    });

    describe('updateMaterials()', () => {
        it.each(data.default())('parse the returned data correctly', async (datum: any) => {
            jest.spyOn(requester, 'put').mockResolvedValue({ data: datum });
            expect(await apiBookings.updateMaterials(datum.entity, datum.id, [] as any)).toMatchSnapshot();
        });
    });
});
