export class Video
{
    constructor(
        public id: String,
        public user_id: String,
        public title: String,
        public description: String,
        public url: String,
        public status: String,
        public updatedAt: String,
        public createdAt: String
    ){}
}