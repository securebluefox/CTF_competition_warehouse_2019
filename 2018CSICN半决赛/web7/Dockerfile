FROM python:2.7

RUN apt-get update

WORKDIR /app

COPY ./www /app

ADD requirement.pip ./

RUN pip install --upgrade pip
RUN pip install -r ./requirement.pip

RUN python sshop/models.py
CMD python main.py