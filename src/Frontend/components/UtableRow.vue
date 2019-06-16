<template>
    <div
        :class="['utable__row', {'utable__row--mine': isMine}]"
    >
        <div class="utable__row-left">
            <div class="utable__line">
                <div class="utable__num utable__col-num">{{i+1}}</div>
                <div class="utable__player utable__col-player">
                    <a
                        :href="player.profileUrl"
                        target="_blank"
                    >{{player.profileName}}</a>
                </div>
                <a
                    :href="'https://store.steampowered.com/app/'+game.id+'/'"
                    target="_blank"
                    class="utable__game-title"
                >{{game.name}}</a>
            </div>

            <div
                v-if="errors"
                class="utable__errors"
            >
                <messagebox
                    mode="error"
                    v-for="(error, key) in errors"
                    :key="'error_'+eventEntry.id+'_'+key"
                >{{error.message}}</messagebox>
            </div>

            <template v-if="!isEditMode">
                <div class="utable__line utable__line--next">
                    <div class="utable__category">
                        <div title="The subcategory for the game to qualify for the event">
                            <i class="icon-fa icon-fa--inline far fa-flag"></i>
                            {{category.name}}
                        </div>
                    </div>

                    <div class="utable__play-stats utable__col-stats">

                        <div
                            v-if="game.achievementsCnt === 0"
                            class="utable__play-stat"
                            title="This game doesn't have any achievements"
                        >
                            <i class="icon-fa icon-fa--inactive fas fa-trophy"></i>
                            <span>&mdash;</span>
                        </div>

                        <div
                            v-else
                            class="utable__play-stat"
                            title="Achievements count"
                        >
                            <i
                                :class="[
                                'icon-fa', 'fas', 'fa-trophy',
                                (eventEntry.achievementsCnt > 0) ? 'icon-fa--golden' : 'icon-fa--gray'
                            ]"
                            ></i>
                            <a
                                :href="player.profileUrl+'stats/'+game.id+'/achievements/'"
                                target="_blank"
                            >{{+eventEntry.achievementsCnt}}/{{+game.achievementsCnt}}</a>
                        </div>

                        <div
                            :title="calcPreciseTime(eventEntry.playTime)"
                            class="utable__play-stat"
                        >
                            <i
                                :class="[
                                'icon-fa', 'fas', 'fa-clock',
                                (eventEntry.playTime > 0) ? 'icon-fa--golden' : 'icon-fa--gray'
                            ]"
                            ></i>
                            {{playtimeFormatted}}h
                        </div>

                        <div
                            v-if="hasInitialStats"
                            class="utable__stats-before"
                        >
                            <div class="utable__stats-before-header">Stats before the start of the event:</div>
                            <div class="utable__play-stat">
                                <i class="icon-fa fas fa-trophy"></i>
                                <span v-if="game.achievementsCnt === 0">&mdash;</span>
                                <span v-else>{{+eventEntry.achievementsCntInitial}}</span>
                            </div>
                            <div
                                :title="calcPreciseTime(eventEntry.playTimeInitial)"
                                class="utable__play-stat"
                            >
                                <i class="icon-fa fas fa-clock"></i>
                                {{playtimeInitialFormatted}}h
                            </div>
                        </div>
                    </div>

                    <div
                        :class="['utable__status', 'utable__col-status', 'utable__status--'+playStatus.id]"
                        title="Played status (Unfinished/Beaten/Completed)"
                    >{{playStatus.name}}</div>

                </div>

                <div
                    v-html="compiledNotes"
                    class="utable__notes text"
                ></div>

                <div class="utable__line utable__line--spaced utable__line--next">
                    <div
                        v-if="eventEntry.verified"
                        class="utable__verified"
                    >
                        <i class="icon-fa icon-fa--inline fas fa-gamepad"></i>Game verified
                    </div>
                    <a
                        v-else-if="updateVerificationAllowed"
                        @click.prevent="setVerify('verified')"
                        class="utable__link-control"
                    >
                        <i class="icon-fa icon-fa--inline fas fa-gamepad"></i>Verify game
                    </a>

                    <template v-if="eventEntry.playStatus !== 'unfinished'">
                        <div
                            v-if="eventEntry.playStatusVerified"
                            class="utable__verified"
                        >
                            <i class="icon-fa icon-fa--inline fas fa-clipboard-check"></i>Played status verified
                        </div>
                        <a
                            v-else-if="updateVerificationAllowed"
                            @click.prevent="setVerify('playStatusVerified')"
                            class="utable__link-control"
                        >
                            <i class="icon-fa icon-fa--inline fas fa-clipboard-check"></i>Verify played status
                        </a>
                    </template>
                </div>

            </template>

            <template v-else>
                <div class="utable__line utable__line--next">
                    <div class="utable__category">
                        <div class="form__block">
                            <label class="form__label" for="p_category">
                                <i class="icon-fa far fa-flag"></i>
                                Subcategory:
                            </label>
                            <select
                                v-model="editValues.category"
                                required
                                name="p_category" id="p_category" class="form__select"
                                title="The subcategory for the game to qualify for the event"
                            >
                                <option
                                    v-for="(category, key) in gameCategories"
                                    :key="'category_'+key"
                                    :value="category.id"
                                >{{category.name}}</option>
                            </select>
                        </div>
                    </div>

                    <div class="utable__col-stats-edit">
                        <label class="form__label">Play stats</label>
                        <div class="form__block form__block--nowrap">
                            <div
                                v-if="game.achievementsCnt === 0"
                                class="utable__play-stat"
                                title="This game doesn't have any achievements"
                            >
                                <i class="icon-fa icon-fa--inactive fas fa-trophy"></i>
                                <span>&mdash;</span>
                            </div>
                            <div
                                v-else
                                class="form__line-block"
                            >
                                <label for="p_ach_count" title="Achievements count">
                                    <i class="icon-fa icon-fa--inline icon-fa--golden fas fa-trophy"></i>
                                </label>
                                <input
                                    v-model.number="editValues.achievementsCnt"
                                    type="text"
                                    name="p_ach_count"
                                    id="p_ach_count"
                                    class="form__input form__input--tiny"
                                    placeholder="0"
                                />
                                <span title="Total count of game achievements">/{{game.achievementsCnt}}</span>
                            </div>
                            <div class="form__line-block">
                                <label for="p_playtime" title="Played time">
                                    <i class="icon-fa icon-fa--inline icon-fa--gray fas fa-clock"></i>
                                </label>
                                <input
                                    v-model="editValues.playTimeFormatted"
                                    type="text"
                                    name="p_playtime"
                                    id="p_playtime"
                                    class="form__input form__input--tiny"
                                    placeholder="0.0"
                                />
                                <span>hours</span>
                            </div>
                        </div>

                        <label class="form__label">Stats before the start of the event:</label>
                        <div class="form__block form__block--nowrap">
                            <div
                                v-if="game.achievementsCnt === 0"
                                class="utable__play-stat"
                                title="This game doesn't have any achievements"
                            >
                                <i class="icon-fa icon-fa--inactive fas fa-trophy"></i>
                                <span>&mdash;</span>
                            </div>
                            <div
                                v-else
                                class="form__line-block"
                            >
                                <label for="ach_count_initial" title="Count of achievements taken before the event">
                                    <i class="icon-fa icon-fa--inline icon-fa--lightblue fas fa-trophy"></i>
                                </label>
                                <input
                                    v-model.number="editValues.achievementsCntInitial"
                                    type="text"
                                    name="ach_count_initial"
                                    id="ach_count_initial"
                                    class="form__input form__input--tiny"
                                    placeholder="0"
                                />
                                <span title="Total count of game achievements">/{{game.achievementsCnt}}</span>
                            </div>
                            <div class="form__line-block">
                                <label for="playtime_initial" title="Played time before the event">
                                    <i class="icon-fa icon-fa--inline icon-fa--lightblue fas fa-clock"></i>
                                </label>
                                <input
                                    v-model="editValues.playTimeInitialFormatted"
                                    type="text"
                                    name="playtime_initial"
                                    id="playtime_initial"
                                    class="form__input form__input--tiny"
                                    placeholder="0.0"
                                />
                                <span>hours</span>
                            </div>
                        </div>
                    </div>

                    <div class="utable__col-status">
                        <div class="form__block">
                            <label class="form__label" for="p_status">
                                <i class="icon-fa fas fa-clipboard-check"></i>
                                Played Status:
                            </label>
                            <select
                                v-model="editValues.playStatus"
                                required
                                name="p_status"
                                id="p_status"
                                class="form__select"
                                title="Played status (Unfinished/Beaten/Completed)"
                            >
                                <option
                                    v-for="(playStatus, key) in playStatuses"
                                    :key="'play_status_'+key"
                                    :value="playStatus.id"
                                >{{playStatus.name}}</option>
                            </select>
                        </div>
                    </div>

                </div>

                <div class="form__block utable__line--next">
                    <label class="form__label" for="p_notes">
                        <i class="icon-fa far fa-comment"></i>
                        Notes:
                    </label>
                    <textarea
                        v-model="editValues.notes"
                        name="p_notes"
                        id="p_notes"
                        class="form__textarea form__textarea--small"
                    ></textarea>
                </div>

            </template>

        </div>
        <div class="utable__row-right">
            <div

                class="utable__controls"
            >
                <template v-if="!isEditMode">
                    <a
                        v-if="editEntryAllowed"
                        @click.prevent="setEditMode"
                        class="utable__link-control">
                        <i class="icon-fa icon-fa--inline fas fa-pencil-alt"></i>Edit
                    </a>
                    <a
                        v-if="deleteEntryAllowed"
                        @click.prevent="remove(eventEntry)"
                        class="utable__link-control"
                    >
                        <i class="icon-fa icon-fa--inline fas fa-times"></i>Delete
                    </a>
                </template>
                <template v-else>
                    <a
                        @click.prevent="submitEdits"
                        class="utable__link-control"
                    >
                        <i class="icon-fa icon-fa--inline fas fa-check"></i>Submit
                    </a>
                    <a
                        @click.prevent="cancelEditMode"
                        class="utable__link-control"
                    >
                        <i class="icon-fa icon-fa--inline fas fa-window-close"></i>Cancel
                    </a>
                </template>
            </div>
            <a
                :href="'https://store.steampowered.com/app/'+game.id+'/'"
                target="_blank"
                class="utable__game-pic"
            >
                <img
                    class="utable__game-img"
                    :src="'https://steamcdn-a.akamaihd.net/steam/apps/'+game.id+'/capsule_184x69.jpg'"
                    :alt="game.name"
                />
            </a>
        </div>

    </div>
</template>

<script>
    import Messagebox from "./Messagebox";
    import marked from 'marked';
    import { mapActions } from 'vuex'

    export default {
        name: "UtableRow",
        components: {Messagebox},
        props: {
            eventEntry: {
                type: Object,
                default: {
                    id: 0,
                    user: 0,
                    event: 0,
                    game: 0,
                    achievementsCnt: 0,
                    playTime: 0,
                    category: 0,
                    playStatus: 'unfinished',
                    notes: '',
                    verified: false,
                    playStatusVerified: false
                }
            },
            i: {
                type: Number,
                default: 0
            },
            isMine: {
                type: Boolean,
                default: false
            },
            gameCategories: {
                type: Array,
                default: []
            },
            playStatuses: {
                type: Array,
                default: []
            }
        },
        computed: {
            playStatus: function() {
                return this.$store.getters.getPlayStatus(this.eventEntry.playStatus);
            },
            category: function () {
                return this.$store.getters.getGameCategory(this.eventEntry.category);
            },
            player: function () {
                return this.$store.getters.getUser(this.eventEntry.player);
            },
            game: function () {
                return this.$store.getters.getGame(this.eventEntry.game)
            },
            playtimeFormatted: function () {
                return (this.eventEntry.playTime / 60).toFixed(1);
            },
            playtimeInitialFormatted: function () {
              return (this.eventEntry.playTimeInitial / 60).toFixed(1);
            },
            compiledNotes: function () {
                return marked(this.eventEntry.notes, {sanitize: true, breaks: true});
            },
            hasInitialStats: function () {
                return (+this.eventEntry.playTimeInitial !== 0) ? true : (+this.eventEntry.achievementsCntInitial !== 0);
            },
            editEntryAllowed: function () {
                return this.$store.getters.isEditingTheEntryAllowed(this.eventEntry.id);
            },
            deleteEntryAllowed: function () {
                return this.$store.getters.isDeletingTheEntryAllowed(this.eventEntry.id);
            },
            updateVerificationAllowed: function () {
                return this.$store.getters.isUpdatingVerificationForTheEntryAllowed(this.eventEntry.id);
            }
        },
        data: function () {
            return {
                isEditMode: false,
                editValues: {},
                errors: []
            };
        },
        methods: {
            ...mapActions(['deleteEventEntry', 'sendEventEntry', 'verifyEntry']),

            remove(entry) {
                let confirmation = confirm(`${this.game.name}\n\nAre you sure you want to delete this entry?`);

                if (confirmation)
                {
                    this.deleteEventEntry(entry).then(() => {
                       //console.log('deleted');
                    });
                }

            },

            setEditMode() {
                this.isEditMode = true;
                this.editValues = {
                    ...this.eventEntry,
                    playTimeFormatted: this.playtimeFormatted,
                    playTimeInitialFormatted: this.playtimeInitialFormatted
                };
            },

            cancelEditMode() {
                this.isEditMode = false;
                this.errors = [];
            },

            parsePlayTimeInHours(minutes) {
                minutes = minutes.toString().replace(',', '.');
                let hours = parseFloat(minutes) * 60;

                return parseInt(hours);
            },

            calcPreciseTime(value) {
                let hours = parseInt(value / 60);
                let minutes = parseInt(value % 60);

                return `${hours}h ${minutes}m`;
            },

            submitEdits() {
                this.errors = [];

                let eventEntry = {
                    id: this.eventEntry.id,
                    eventId: this.eventEntry.event,
                    category: {id: this.editValues.category},
                    playStatus: this.editValues.playStatus,
                    achievementsCnt: parseInt(this.editValues.achievementsCnt),
                    playTime: this.parsePlayTimeInHours(this.editValues.playTimeFormatted),
                    achievementsCntInitial: parseInt(this.editValues.achievementsCntInitial),
                    playTimeInitial: this.parsePlayTimeInHours(this.editValues.playTimeInitialFormatted),
                    notes: this.editValues.notes
                };

                this.sendEventEntry(eventEntry).then(({errors}) => {

                    if (errors)
                    {
                        this.errors = errors;
                        return;
                    }

                    this.isEditMode = false;
                })
                    .catch(e => {
                        this.errors = e.response.data.errors ? e.response.data.errors : [{message: e}];
                    })
            },

            setVerify(field) {
                this.verifyEntry({
                    id: this.eventEntry.id,
                    event: this.eventEntry.event,
                    field,
                    value: true
                }).then(({errors}) => {
                    if (errors) {
                        this.errors = errors;
                    }
                })
            }
        }
    }
</script>

<style lang="less">

</style>