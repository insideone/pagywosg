import BaseApi from './baseApi'

export default new class extends BaseApi {
    constructor (props) {
        super(props)
        this.url = '/api/games';
    }

    search(q, page) {
        return this.axios.get(this.path(''), {
            params: {q, page}
        })
    }
}
