#coding:utf-8
import os

class Captcha:
    def __init__(self):
        self.root_path = os.path.dirname(os.path.dirname(__file__))
        print(self.root_path)
        self.ans_path = os.path.join(self.root_path, 'static/captcha/ans')
        print(self.ans_path)
        self.jpgs_path = os.path.join(self.root_path, 'static/captcha/jpgs')
        self.files = self.get_files(self.jpgs_path)
        self.uuid = ''
        self.jpg=''
        self.question = ''
    def get_files(self, file_path):
        for root, dirs, files in os.walk(file_path):
            return files

    def get_ans(self, uuid):
        answer = {}
        with open(os.path.join(self.ans_path, 'ans%s.txt' % uuid), 'r',encoding="utf-8") as f:
            for line in f.readlines():
                if line != '\n':
                    ans = line.strip().split('=')
                    answer[ans[0].strip()] = ans[1].strip()
        return answer


    def generate_captcha(self):
        uuids = []
        for file in self.files:
            uuids.append(file.replace('ques', '').replace('.jpg', ''))
        from random import choice
        uuid = choice(uuids)
        ans = self.get_ans(uuid)
        self.uuid = uuid
        self.getjpg()
        self.question = ans['vtt_ques']

    def set_uuid(self,uuid):
        self.uuid = uuid

    def getjpg(self):
        # jpg = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
        jpg = os.path.join("/static/captcha/jpgs/ques"+self.uuid + ".jpg")
        self.jpg=jpg
        #return jpg
    def check(self,x,y):
        try:
            x=float(x)
            y=float(y)
            ans=self.get_ans(self.uuid)
            if x and y :
                if float(ans['ans_pos_x_1']) <= x <= (float(ans['ans_width_x_1']) + float(ans['ans_pos_x_1'])):
                    if float(ans['ans_pos_y_1']) <= y <= (
                            float(ans['ans_height_y_1']) + float(ans['ans_pos_y_1'])):
                        return True
            return False
        except Exception as ex:
            print(str(ex))
            return False

if __name__=="__main__":
    cap=Captcha()
    cap.generate_captcha()
    print(cap.uuid)
    print(cap.question)
    print(cap.get_ans(cap.uuid))